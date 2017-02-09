/**
 * 发送优惠劵通用方法
 * Created by alan on 2016/11/2.
 */
function send_coupon(url, obj,id) {
    var object = $(obj);
    if(!id) {
        var id = object.attr('data-id');
    }
    $.ajax({
        type: "post",
        url: url,
        dataType: 'json',
        data: {'op': 'pay', 'id': id, 'do': 'plugin', 'm': 'manor_shop', 'p': 'coupon', 'method': 'detail'},
        success: function (res) {
            if (res.status == -1) {
                return (res.result ? res.result : '领取失败');
            }
            var logid = res.result.logid;
            return payResult(url,logid, id);
        }
    });
}
function payResult(url, logid, id) {
    $.ajax({
        type: "post",
        url: url,
        dataType: 'json',
        data: {
            'op': 'payresult',
            'id': id,
            'logid': logid,
            'do': 'plugin',
            'm': 'manor_shop',
            'p': 'coupon',
            'method': 'detail'
        },
        success: function (result) {
            return result;
            if (result.status != 1) {
                return '活动未开启';
            } else {
                return 1;
            }
        }
    });
}


