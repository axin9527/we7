//分组发放优惠劵
//这里是领取优惠卷得js聚集地。默认就是一个id参数，先去访问分组，如果存在这个分组，就对应得发放分组下面的优惠卷。如果不存在。就
//下发对应这个id的优惠卷。如果都没有，就提示优惠卷不存在。




$(function () {

});

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
