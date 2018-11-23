var dateFormat = function () {
    var token = /d{1,2}|m{1,3}|yy(?:yy)?|([HhMsTt])\1?|"[^"]*"|'[^']*'/g,
            pad = function (val, len) {
                val = String(val);
                len = len || 2;
                while (val.length < len)
                    val = "0" + val;
                return val;
            };
    return function (date, mask) {
        var dF = dateFormat;
        date = date.toString();
        var y = date.substr(0, 4),
                m = date.substr(5, 2),
                d = date.substr(8, 2),
                H = date.substr(11, 2),
                M = date.substr(14, 2),
                s = date.substr(17, 2),
                flags = {
                    d: d,
                    dd: pad(d),
                    m: m,
                    mm: pad(m),
                    mmm: dF.i18n.monthNames[parseInt(m) - 1],
                    yy: String(y).slice(2),
                    yyyy: y,
                    h: H % 12 || 12,
                    hh: pad(H % 12 || 12),
                    H: H,
                    HH: pad(H),
                    M: M,
                    MM: pad(M),
                    s: s,
                    ss: pad(s),
                    S: ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 !== 10) * d % 10]
                };
        return mask.replace(token, function ($0) {
            return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
        });
    };
}();
dateFormat.i18n = {
    monthNames: [
        "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
    ]
};
// For convenience...
String.prototype.dateFormat = function (mask) {
    return dateFormat(this, mask);
};

