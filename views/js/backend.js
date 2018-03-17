$(document).ready(function () {
    const BOARD_WIDTH_MIN = 130;
    const BOARD_WIDTH_MAX = 2000;

    const SCALE_HEIGHT_MIN = 60;
    const SCALE_HEIGHT_MAX = 4000;

    const SCALE_WIDTH_MIN = 60;
    const SCALE_WIDTH_MAX = 2000;
    
    $("#configuration_form").validate({
        rules: {
            "config[PINTEREST_BOARD_WIDGET_BOARD_WIDTH]": {
                range: [ BOARD_WIDTH_MIN, BOARD_WIDTH_MAX ]
            },
            "config[PINTEREST_BOARD_WIDGET_SCALE_HEIGHT]": {
                range: [ SCALE_HEIGHT_MIN, SCALE_HEIGHT_MAX ]
            },
            "config[PINTEREST_BOARD_WIDGET_SCALE_WIDTH]": {
                range: [ SCALE_WIDTH_MIN, SCALE_WIDTH_MAX ]
            },
            "config[PINTEREST_BOARD_WIDGET_URL]": {
                required: true
            }
        }
    });
});