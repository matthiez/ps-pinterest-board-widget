$(function () {
    $("#configuration_form").validate({
        rules: {
            "config[SHMO_PINTEREST_BOARD_WIDGET_BOARD_WIDTH]": {
                required: false,
                range: [130, 2000]
            },
            "config[SHMO_PINTEREST_BOARD_WIDGET_SCALE_HEIGHT]": {
                required: false,
                range: [60, 4000]
            },
            "config[SHMO_PINTEREST_BOARD_WIDGET_SCALE_WIDTH]": {
                required: false,
                range: [60, 2000]
            },
            "config[SHMO_PINTEREST_BOARD_WIDGET_URL]": {
                required: true
            }
        },
        messages: {
            "config[SHMO_PINTEREST_BOARD_WIDGET_BOARD_WIDTH]": {
                required: "Widget board width: Values between 130 and 2000."
            },
            "config[SHMO_PINTEREST_BOARD_WIDGET_SCALE_HEIGHT]": {
                required: "Widget scale height: Values between 60 and 4000."
            },
            "config[SHMO_PINTEREST_BOARD_WIDGET_SCALE_WIDTH]": {
                required: "Widget scale width: Values between 60 and 2000."
            },
            "config[SHMO_PINTEREST_BOARD_WIDGET_URL]": {
                required: "Board widget URL: You must enter an URL."
            }
        }
    });
});