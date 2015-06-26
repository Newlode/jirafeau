/*
 *  Jirafeau, your web file repository
 *  Copyright (C) 2015  Jerome Jutteau <j.jutteau@gmail.com>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

function show_link (url, reference, delete_code, crypt_key, date)
{
    // Upload finished
    document.getElementById('uploading').style.display = 'none';
    document.getElementById('upload').style.display = 'none';
    document.getElementById('upload_finished').style.display = '';
    document.title = 'Jirafeau - 100%';

    // Download page
    var download_link = url + 'f.php?h=' + reference;
    var download_link_href = url + 'f.php?h=' + reference;
    if (crypt_key.length > 0)
    {
        download_link += '&amp;k=' + crypt_key;
        download_link_href += '&k=' + crypt_key;
    }
    if (!!document.getElementById('upload_finished_download_page'))
    {
        document.getElementById('upload_link').innerHTML = download_link;
        document.getElementById('upload_link').href = download_link_href;
    }

    // Email link
    var filename = document.getElementById('file_select').files[0].name;
    var b = encodeURIComponent("Download file \"" + filename + "\":") + "%0D";
    b += encodeURIComponent(download_link_href) + "%0D";
    if (date)
        b += "%0D" + encodeURIComponent("This file will be available until " + date) + "%0D";
    document.getElementById('upload_link_email').href = "mailto:?body=" + b + "&subject=" + encodeURIComponent(filename);

    // Delete link
    var delete_link = url + 'f.php?h=' + reference + '&amp;d=' + delete_code;
    var delete_link_href = url + 'f.php?h=' + reference + '&d=' + delete_code;
    document.getElementById('delete_link').innerHTML = delete_link;
    document.getElementById('delete_link').href = delete_link_href;

    // Validity date
    if (date)
    {
        document.getElementById('date').innerHTML = date;
        document.getElementById('validity').style.display = '';
    }
    else
        document.getElementById('validity').style.display = 'none';

    // Preview link (if allowed)
    if (!!document.getElementById('preview_link'))
    {
        document.getElementById('upload_finished_preview').style.display = 'none';
        var preview_link = url + 'f.php?h=' + reference + '&amp;p=1';
        var preview_link_href = url + 'f.php?h=' + reference + '&p=1';
        if (crypt_key.length > 0)
        {
            preview_link += '&amp;k=' + crypt_key;
            preview_link_href += '&k=' + crypt_key;
        }

        // Test if content can be previewed
         type = document.getElementById('file_select').files[0].type;
         if (type.indexOf("image") > -1 ||
             type.indexOf("audio") > -1 ||
             type.indexOf("text") > -1 ||
             type.indexOf("video") > -1)
         {
            document.getElementById('preview_link').innerHTML = preview_link;
            document.getElementById('preview_link').href = preview_link_href;
            document.getElementById('upload_finished_preview').style.display = '';
         }
    }

    // Direct download link
    var direct_download_link = url + 'f.php?h=' + reference + '&amp;d=1';
    var direct_download_link_href = url + 'f.php?h=' + reference + '&d=1';
    if (crypt_key.length > 0)
    {
        direct_download_link += '&amp;k=' + crypt_key;
        direct_download_link_href += '&k=' + crypt_key;
    }
    document.getElementById('direct_link').innerHTML = direct_download_link;
    document.getElementById('direct_link').href = direct_download_link_href;


    // Hide preview and direct download link if password is set
    if (document.getElementById('input_key').value.length > 0)
    {
        if (!!document.getElementById('preview_link'))
            document.getElementById('upload_finished_preview').style.display = 'none';
        document.getElementById('upload_direct_download').style.display = 'none';
    }
}

function show_upload_progression (percentage, speed, time_left)
{
    document.getElementById('uploaded_percentage').innerHTML = percentage;
    document.getElementById('uploaded_speed').innerHTML = speed;
    document.getElementById('uploaded_time').innerHTML = time_left;
    document.title = 'Jirafeau - ' + percentage;
}

function hide_upload_progression ()
{
    document.getElementById('uploaded_percentage').style.display = 'none';
    document.getElementById('uploaded_speed').style.display = 'none';
    document.getElementById('uploaded_time').style.display = 'none';
    document.title = 'Jirafeau';
}

function upload_progress (e)
{
    if (!e.lengthComputable)
        return;

    // Init time estimation if needed
    if (upload_time_estimation_total_size == 0)
        upload_time_estimation_total_size = e.total;

    // Compute percentage
    var p = Math.round (e.loaded * 100 / e.total);
    var p_str = ' ';
    if (p != 100)
        p_str = p.toString() + '%';
    // Update estimation speed
    upload_time_estimation_add(e.loaded);
    // Get speed string
    var speed_str = upload_time_estimation_speed_string();
    speed_str = upload_speed_refresh_limiter(speed_str);
    // Get time string
    var time_str = chrono_update(upload_time_estimation_time());

    show_upload_progression (p_str, speed_str, time_str);
}

function control_selected_file_size(max_size, error_str)
{
    f_size = document.getElementById('file_select').files[0].size;
    if (max_size > 0 && f_size > max_size * 1024 * 1024)
    {
        pop_failure(error_str);
        document.getElementById('send').style.display = 'none';
    }
    else
    {
        document.getElementById('options').style.display = '';
        document.getElementById('send').style.display = '';
        document.getElementById('error_pop').style.display = 'none';
        document.getElementById('file_select').style.left = 'inherit';
        document.getElementById('file_select').style.height = 'inherit';
        document.getElementById('file_select').style.opacity = '1';
    }
}

function pop_failure (e)
{
    var text = "An error occured";
    if (typeof e !== 'undefined')
        text = e;
    text = "<p>" + text + "</p>";
    document.getElementById('error_pop').innerHTML = e;

    document.getElementById('uploading').style.display = 'none';
    document.getElementById('error_pop').style.display = '';
    document.getElementById('upload').style.display = '';
    document.getElementById('send').style.display = '';
}

function classic_upload (url, file, time, password, one_time, upload_password)
{
    // Delay time estimation init as we can't have file size
    upload_time_estimation_init(0);

    var req = new XMLHttpRequest ();
    req.upload.addEventListener ("progress", upload_progress, false);
    req.addEventListener ("error", pop_failure, false);
    req.addEventListener ("abort", pop_failure, false);
    req.onreadystatechange = function ()
    {
        if (req.readyState == 4 && req.status == 200)
        {
            var res = req.responseText;
            if (res == "Error")
            {
                pop_failure ();
                return;
            }
            res = res.split ("\n");
            if (time != 'none')
            {
                var d = new Date();
                if (time == 'minute')
                    d.setSeconds (d.getSeconds() + 60);
                else if (time == 'hour')
                    d.setSeconds (d.getSeconds() + 3600);
                else if (time == 'day')
                    d.setSeconds (d.getSeconds() + 86400);
                else if (time == 'week')
                    d.setSeconds (d.getSeconds() + 604800);
                else if (time == 'month')
                    d.setSeconds (d.getSeconds() + 2419200);
                else if (time == 'year')
                    d.setSeconds (d.getSeconds() + 29030400);
                else
                    return;
                show_link (url, res[0], res[1], res[2], d.toString());
            }
            else
                show_link (url, res[0], res[1], res[2]);
        }
    }
    req.open ("POST", url + 'script.php' , true);

    var form = new FormData();
    form.append ("file", file);
    if (time)
        form.append ("time", time);
    if (password)
        form.append ("key", password);
    if (one_time)
        form.append ("one_time_download", '1');
    if (upload_password.length > 0)
        form.append ("upload_password", upload_password);

    req.send (form);
}

function check_html5_file_api ()
{
    if (window.File && window.FileReader && window.FileList && window.Blob)
        return true;
    return false;
}

var async_global_transfered = 0;
var async_global_url = '';
var async_global_file;
var async_global_ref = '';
var async_global_max_size = 0;
var async_global_time;
var async_global_transfering = 0;

function async_upload_start (url, max_size, file, time, password, one_time, upload_password)
{
    async_global_transfered = 0;
    async_global_url = url;
    async_global_file = file;
    async_global_max_size = max_size;
    async_global_time = time;

    var req = new XMLHttpRequest ();
    req.addEventListener ("error", pop_failure, false);
    req.addEventListener ("abort", pop_failure, false);
    req.onreadystatechange = function ()
    {
        if (req.readyState == 4 && req.status == 200)
        {
            var res = req.responseText;
            if (res == "Error")
            {
                pop_failure ();
                return;
            }
            res = res.split ("\n");
            async_global_ref = res[0];
            var code = res[1];
            async_upload_push (code);
        }
    }
    req.open ("POST", async_global_url + 'script.php?init_async' , true);

    var form = new FormData();
    form.append ("filename", async_global_file.name);
    form.append ("type", async_global_file.type);
    if (time)
        form.append ("time", time);
    if (password)
        form.append ("key", password);
    if (one_time)
        form.append ("one_time_download", '1');
    if (upload_password.length > 0)
        form.append ("upload_password", upload_password);

    // Start time estimation
    upload_time_estimation_init(async_global_file.size);

    req.send (form);
}

function async_upload_progress (e)
{
    if (!e.lengthComputable && async_global_file.size != 0)
        return;

    // Compute percentage
    var p = Math.round ((e.loaded + async_global_transfered) * 100 / (async_global_file.size));
    var p_str = ' ';
    if (p != 100)
        p_str = p.toString() + '%';
    // Update estimation speed
    upload_time_estimation_add(e.loaded + async_global_transfered);
    // Get speed string
    var speed_str = upload_time_estimation_speed_string();
    speed_str = upload_speed_refresh_limiter(speed_str);
    // Get time string
    var time_str = chrono_update(upload_time_estimation_time());

    show_upload_progression (p_str, speed_str, time_str);
}

function async_upload_push (code)
{
    if (async_global_transfered == async_global_file.size)
    {
        hide_upload_progression ();
        async_upload_end (code);
        return;
    }
    var req = new XMLHttpRequest ();
    req.upload.addEventListener ("progress", async_upload_progress, false);
    req.addEventListener ("error", pop_failure, false);
    req.addEventListener ("abort", pop_failure, false);
    req.onreadystatechange = function ()
    {
        if (req.readyState == 4 && req.status == 200)
        {
            var res = req.responseText;
            if (res == "Error")
            {
                pop_failure ();
                return;
            }
            res = res.split ("\n");
            var code = res[0]
            async_global_transfered = async_global_transfering;
            async_upload_push (code);
        }
    }
    req.open ("POST", async_global_url + 'script.php?push_async' , true);

    var chunk_size = parseInt (async_global_max_size * 0.50);
    var start = async_global_transfered;
    var end = start + chunk_size;
    if (end >= async_global_file.size)
        end = async_global_file.size;
    var blob = async_global_file.slice (start, end);
    async_global_transfering = end;

    var form = new FormData();
    form.append ("ref", async_global_ref);
    form.append ("data", blob);
    form.append ("code", code);
    req.send (form);
}

function async_upload_end (code)
{
    var req = new XMLHttpRequest ();
    req.addEventListener ("error", pop_failure, false);
    req.addEventListener ("abort", pop_failure, false);
    req.onreadystatechange = function ()
    {
        if (req.readyState == 4 && req.status == 200)
        {
            var res = req.responseText;
            if (res == "Error")
            {
                pop_failure ();
                return;
            }
            res = res.split ("\n");
            if (async_global_time != 'none')
            {
                var d = new Date();
                if (async_global_time == 'minute')
                    d.setSeconds (d.getSeconds() + 60);
                else if (async_global_time == 'hour')
                    d.setSeconds (d.getSeconds() + 3600);
                else if (async_global_time == 'day')
                    d.setSeconds (d.getSeconds() + 86400);
                else if (async_global_time == 'week')
                    d.setSeconds (d.getSeconds() + 604800);
                else if (async_global_time == 'month')
                    d.setSeconds (d.getSeconds() + 2419200);
                else if (async_global_time == 'year')
                    d.setSeconds (d.getSeconds() + 29030400);
                else
                    return;
                show_link (async_global_url, res[0], res[1], res[2], d.toString());
            }
            else
                show_link (async_global_url, res[0], res[1], res[2]);
        }
    }
    req.open ("POST", async_global_url + 'script.php?end_async' , true);

    var form = new FormData();
    form.append ("ref", async_global_ref);
    form.append ("code", code);
    req.send (form);
}

function upload (url, max_size)
{
    if (check_html5_file_api ()
        && document.getElementById('file_select').files[0].size >= max_size)
    {
        async_upload_start (url,
            max_size,
            document.getElementById('file_select').files[0],
            document.getElementById('select_time').value,
            document.getElementById('input_key').value,
            document.getElementById('one_time_download').checked,
            document.getElementById('upload_password').value
            );
    }
    else
    {
        classic_upload (url,
            document.getElementById('file_select').files[0],
            document.getElementById('select_time').value,
            document.getElementById('input_key').value,
            document.getElementById('one_time_download').checked,
            document.getElementById('upload_password').value
            );
    }
}

var upload_time_estimation_total_size = 42;
var upload_time_estimation_transfered_size = 42;
var upload_time_estimation_transfered_date = 42;
var upload_time_estimation_moving_average_speed = 42;

function upload_time_estimation_init(total_size)
{
    upload_time_estimation_total_size = total_size;
    upload_time_estimation_transfered_size = 0;
    upload_time_estimation_moving_average_speed = 0;
    var d = new Date();
    upload_time_estimation_transfered_date = d.getTime();
}

function upload_time_estimation_add(total_transfered_size)
{
    // Let's compute the current speed
    var d = new Date();
    var speed = upload_time_estimation_moving_average_speed;
    if (d.getTime() - upload_time_estimation_transfered_date != 0)
        speed = (total_transfered_size - upload_time_estimation_transfered_size)
                / (d.getTime() - upload_time_estimation_transfered_date);
    // Let's compute moving average speed on 30 values
    var m = (upload_time_estimation_moving_average_speed * 29 + speed) / 30;
    // Update global values
    upload_time_estimation_transfered_size = total_transfered_size;
    upload_time_estimation_transfered_date = d.getTime();
    upload_time_estimation_moving_average_speed = m;
}

function upload_time_estimation_speed_string()
{
    // speed ms -> s
    var s = upload_time_estimation_moving_average_speed * 1000;
    var res = 0;
    var scale = '';
    if (s <= 1000)
    {
        res = s.toString();
        scale = "o/s";
    }
    else if (s < 1000000)
    {
        res = Math.floor(s/100) / 10;
        scale = "Ko/s";
    }
    else
    {
        res = Math.floor(s/100000) / 10;
        scale = "Mo/s";
    }
    if (res == 0)
        return '';
    else
        return res.toString() + ' ' + scale;
}

function milliseconds_to_time_string (milliseconds)
{
    function numberEnding (number) {
        return (number > 1) ? 's' : '';
    }

    var temp = Math.floor(milliseconds / 1000);
    var years = Math.floor(temp / 31536000);
    if (years) {
        return years + ' year' + numberEnding(years);
    }
    var days = Math.floor((temp %= 31536000) / 86400);
    if (days) {
        return days + ' day' + numberEnding(days);
    }
    var hours = Math.floor((temp %= 86400) / 3600);
    if (hours) {
        return hours + ' hour' + numberEnding(hours);
    }
    var minutes = Math.floor((temp %= 3600) / 60);
    if (minutes) {
        return minutes + ' minute' + numberEnding(minutes);
    }
    var seconds = temp % 60;
    if (seconds) {
        return seconds + ' second' + numberEnding(seconds);
    }
    return 'less than a second';
}

function upload_time_estimation_time()
{
    // Estimate remaining time
    if (upload_time_estimation_moving_average_speed == 0)
        return 0;
    return (upload_time_estimation_total_size - upload_time_estimation_transfered_size)
            / upload_time_estimation_moving_average_speed;
}

var chrono_last_update = 0;
var chrono_time_ms = 0;
var chrono_time_ms_last_update = 0;
function chrono_update(time_ms)
{
    var d = new Date();
    var chrono = 0;
    // Don't update too often
    if (d.getTime() - chrono_last_update < 3000 &&
        chrono_time_ms_last_update > 0)
        chrono = chrono_time_ms;
    else
    {
        chrono_last_update = d.getTime();
        chrono_time_ms = time_ms;
        chrono = time_ms;
        chrono_time_ms_last_update = d.getTime();
    }

    // Adjust chrono for smooth estimation
    chrono = chrono - (d.getTime() - chrono_time_ms_last_update);

    // Let's update chronometer
    var time_str = '';
    if (chrono > 0)
        time_str = milliseconds_to_time_string (chrono);
    return time_str;
}

var upload_speed_refresh_limiter_last_update = 0;
var upload_speed_refresh_limiter_last_value = '';
function upload_speed_refresh_limiter(speed_str)
{
    var d = new Date();
    if (d.getTime() - upload_speed_refresh_limiter_last_update > 1500)
    {
        upload_speed_refresh_limiter_last_value = speed_str;
        upload_speed_refresh_limiter_last_update = d.getTime();
    }
    return upload_speed_refresh_limiter_last_value;
}
