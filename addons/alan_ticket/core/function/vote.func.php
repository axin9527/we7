<?php

/**
 * 前xxx排行榜 例如 前100排行榜
 * @param $limit
 * @return array
 */
function vote_range($limit)
{
    $sql = "select * from ims_ticket_range where isshield = 0 order by vote_number desc limit $limit";
    $result1 = pdo_fetchall($sql);
    $sql = "select * from ims_ticket_range_mock order by vote_number desc limit $limit";
    $result2 = pdo_fetchall($sql);

    return range_join($result1, $result2);
}

/**
 * 合并真榜和假榜
 * @param $range1
 * @param $range2
 * @return array
 */
function range_join($range1, $range2)
{
    $dict = [];
    foreach ($range1 as $range) {
        $dict[$range['user_id']] = $range;
    }
    foreach ($range2 as $range) {
        $user_id = $range["user_id"];
        if (array_key_exists($user_id, $dict)) {
            $old_range = &$dict[$user_id];
            $old_range['vote_number'] += $range['vote_number'];
        } else {
            $dict[$user_id] = $range;
        }
    }
    $new_range = array_values($dict);
    usort($new_range, function ($x, $y) {
        if ($y['vote_number'] == $x['vote_number']) {
            return $x['updated_time'] > $y['updated_time'];
        }

        return $y['vote_number'] > $x['vote_number'];
    });

    return $new_range;
}


/**
 * 我的排名
 * @param $id
 * @return int
 */
function my_range($id)
{
    $ranges = vote_range_all();
    $count = count($ranges);
    for ($i = 0; $i < $count; $i++) {
        if ($ranges[$i]["user_id"] == $id) {
            return $i + 1;
        }
    }

    return $count + 1;
}

/**
 * 所有的排行榜
 * @return array
 */
function vote_range_all()
{
    $sql = "select * from ims_ticket_range where isshield = 0 order by vote_number desc, updated_time";
    $result1 = pdo_fetchall($sql);
    $sql = "select * from ims_ticket_range_mock order by vote_number desc";
    $result2 = pdo_fetchall($sql);

    return range_join($result1, $result2);
}

/**
 * 所有的排行榜
 * @return array
 */
function vote_range_all_back()
{
    $sql = "select * from ims_ticket_range  order by vote_number desc, updated_time";
    $result1 = pdo_fetchall($sql);
    $sql = "select * from ims_ticket_range_mock order by vote_number desc";
    $result2 = pdo_fetchall($sql);

    return range_join($result1, $result2);
}

/**
 * 我的投票数
 * @param $id
 * @return bool
 */
function my_vote_number($id)
{
    $realNumber = pdo_fetchcolumn("select vote_number from ims_ticket_range where user_id = :id",
        [':id' => $id]);
    $mockNumber = pdo_fetchcolumn("select vote_number from ims_ticket_range_mock where user_id = :id",
        [':id' => $id]);

    return $realNumber + $mockNumber;
}

/**
 * 我的投票人
 * @param $id
 * @param $start
 * @param $limit
 * @return array
 */
function my_friend($id, $start, $limit)
{
    $sql = "select user.*,vote.vote_time 
        from ims_ticket_vote as vote 
        left join ims_ticket_user as user 
            on user.id = vote.from_id 
        where to_id = :id and user.ismock=0
        order by vote.vote_time desc";
    $result = pdo_fetchall($sql, [":id" => $id]);
    $number = pdo_fetchcolumn("select vote_number from ims_ticket_range_mock where user_id = :id", [":id" => $id]);
    if ($number) {
        $sql = "select *, 0 as vote_time from ims_ticket_user where ismock = 1 limit $number";
        $result_mock = pdo_fetchall($sql);

        return array_slice(
            array_merge($result, $result_mock)
            , $start, $limit);
    } else {
        return array_slice($result, 0, $limit);
    }


}

/**
 * 投票
 * @param $from
 * @param $to
 * @return bool
 */
function vote_add($from, $to)
{
    pdo_begin();
    $is_exists = pdo_fetchcolumn("select count(id) from ims_ticket_vote where from_id = :from_id and to_id = :to_id",
        array(
            ":from_id" => $from,
            ":to_id"   => $to
        ));
    if ($is_exists > 0) {
        pdo_commit();

        return false;
    } else {
        $ret = pdo_insert('ticket_vote', array(
            "from_id"   => $from,
            "to_id"     => $to,
            "vote_time" => time()
        ));
        if (!$ret) {
            pdo_rollback();

            return false;
        }
    }
    $is_exists = pdo_fetchcolumn("select count(id) from ims_ticket_range where user_id = :to_id",
        [
            ":to_id" => $to
        ]);

    if ($is_exists > 0) {
        $ret = pdo_query("update ims_ticket_range set vote_number = vote_number + 1, updated_time=:now  where user_id = :user_id",
            [
                "user_id" => $to,
                "now"     => time()
            ]);
        if (!$ret) {
            pdo_rollback();

            return false;
        }
    } else {
        $users = pdo_fetchall("select * from ims_ticket_user where id = :id", [":id" => $to]);
        $user = $users[0];
        $ret = pdo_insert("ticket_range", [
            "vote_number"  => 1,
            "user_id"      => $user["id"],
            "headimg"      => $user["headimg"],
            "nickname"     => $user["nickname"],
            "wish"         => $user["wish"],
            "updated_time" => time()
        ]);
        if (!$ret) {
            pdo_rollback();

            return false;
        }
    }
    pdo_commit();

    return true;
}