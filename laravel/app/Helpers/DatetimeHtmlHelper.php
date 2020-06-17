<?php

use Carbon\CarbonImmutable;

if (!function_exists('datetime_html_weekday')) {
    /**
     * 曜日を表示するHTMLを返す
     */
    function datetime_html_weekday(CarbonImmutable $carbon) : string
    {
        $setting = [
            ['disp' => '(日)', 'color' => 'red'],
            ['disp' => '(月)'],
            ['disp' => '(火)'],
            ['disp' => '(水)'],
            ['disp' => '(木)'],
            ['disp' => '(金)'],
            ['disp' => '(土)', 'color' => 'blue'],
        ];

        $set = $setting[$carbon->dayOfWeek];
        $color = !isset($set['color']) ? 'black' : $set['color'];
        return "<span style='color:" . $color . ";'>" . $set['disp'] . "</span>";
    }
}
