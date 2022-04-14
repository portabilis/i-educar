var TextArea = (function () {

    var _init = function (el, options) {

        let defaultOptions = {
            feedbackText: '{c}/{m}',
            max: 380
        };

        Object.assign(defaultOptions, options);

        el.maxlength(defaultOptions);
    };

    return {
        init: _init
    }
})();