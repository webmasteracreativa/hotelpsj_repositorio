<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>
<div class="bookme-pro-holidays-nav">
    <div class="input-group input-group-lg">
        <div class="input-group-btn">
            <button class="btn btn-default bookme-pro-js-jCalBtn" data-trigger=".jCal .left" type="button">
                <i class="dashicons dashicons-arrow-left-alt2"></i>
            </button>
        </div>
        <input class="form-control text-center jcal_year" id="appendedPrependedInput"
               readonly type="text" value="">
        <div class="input-group-btn">
            <button class="btn btn-default bookme-pro-js-jCalBtn" data-trigger=".jCal .right" type="button">
                <i class="dashicons dashicons-arrow-right-alt2"></i>
            </button>
        </div>
    </div>
</div>

<div class="bookme-pro-js-annual-calendar bookme-pro-margin-top-lg jCal-wrap"></div>