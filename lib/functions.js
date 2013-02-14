/*
 *  Jirafeau, your web file repository
 *  Copyright (C) 2012  Jerome Jutteau <j.jutteau@gmail.com>
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

function show_link (url, reference, delete_code, date)
{
    var download_link = url + 'file.php?h=' + reference;
    var delete_link = download_link + '&amp;d=' + delete_code;
    var delete_link_href = download_link + '&d=' + delete_code;
    document.getElementById('upload_link').innerHTML = download_link;
    document.getElementById('upload_link').href = download_link;
    document.getElementById('delete_link').innerHTML = delete_link;
    document.getElementById('delete_link').href = delete_link_href;
    if (date)
    {
        document.getElementById('date').innerHTML = date;
        document.getElementById('validity').style.display = '';
    }
    else
        document.getElementById('validity').style.display = 'none';

    document.getElementById('uploading').style.display = 'none';
    document.getElementById('upload').style.display = 'none';
    document.getElementById('upload_finished').style.display = '';
}

function show_upload_progression (p)
{
    document.getElementById('uploaded_percentage').innerHTML = p;
    document.title = 'Jirafeau - ' + p;
}

function upload_progress (e)
{
    if (!e.lengthComputable)
        return;
    /* Show the user the operation do not reach 100%, the server need time
     * to give a response before providing the link.
     */
    var p = Math.round (e.loaded * 99 / e.total);
    show_upload_progression (p.toString() + '%');
}

function upload_failed (e)
{
    /* Todo: Considere showing a error div. */
    alert ('Sorry, upload failed');
}

function classic_upload (url, file, time, password, one_time)
{
    var req = new XMLHttpRequest ();
    req.upload.addEventListener ("progress", upload_progress, false);
    req.addEventListener ("error", upload_failed, false);
    req.addEventListener ("abort", upload_failed, false);
    req.onreadystatechange = function ()
    {
        if (req.readyState == 4 && req.status == 200)
        {
            var res = req.responseText;
            if (res == "Error")
                return;
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
                else
                    return;
                show_link (url, res[0], res[1], d.toString());
            }
            else
                show_link (url, res[0], res[1]);
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

function async_upload_start (url, max_size, file, time, password, one_time)
{
    async_global_transfered = 0;
    async_global_url = url;
    async_global_file = file;
    async_global_max_size = max_size;
    async_global_time = time;

    var req = new XMLHttpRequest ();
    req.addEventListener ("error", upload_failed, false);
    req.addEventListener ("abort", upload_failed, false);
    req.onreadystatechange = function ()
    {
        if (req.readyState == 4 && req.status == 200)
        {
            var res = req.responseText;
            if (res == "Error")
                return;
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
    req.send (form);
}

function async_upload_progress (e)
{
    if (!e.lengthComputable && async_global_file.size != 0)
        return;
    var p = Math.round ((e.loaded + async_global_transfered) * 99 / (async_global_file.size));
    show_upload_progression (p.toString() + '%');
}

function async_upload_push (code)
{
    if (async_global_transfered == async_global_file.size)
    {
        async_upload_end (code);
        return;
    }
    var req = new XMLHttpRequest ();
    req.upload.addEventListener ("progress", async_upload_progress, false);
    req.addEventListener ("error", upload_failed, false);
    req.addEventListener ("abort", upload_failed, false);
    req.onreadystatechange = function ()
    {
        if (req.readyState == 4 && req.status == 200)
        {
            var res = req.responseText;
            if (res == "Error")
                return;
            res = res.split ("\n");
            var code = res[0]
            async_global_transfered = async_global_transfering;
            async_upload_push (code);
        }
    }
    req.open ("POST", async_global_url + 'script.php?push_async' , true);

    var chunk_size = parseInt (async_global_max_size * 0.90);
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
    req.addEventListener ("error", upload_failed, false);
    req.addEventListener ("abort", upload_failed, false);
    req.onreadystatechange = function ()
    {
        if (req.readyState == 4 && req.status == 200)
        {
            var res = req.responseText;
            if (res == "Error")
                return;
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
                else
                    return;
                show_link (async_global_url, res[0], res[1], d.toString());
            }
            else
                show_link (async_global_url, res[0], res[1]);
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
            document.getElementById('one_time_download').checked
            );
    }
    else
    {
        classic_upload (url,
            document.getElementById('file_select').files[0],
            document.getElementById('select_time').value,
            document.getElementById('input_key').value,
            document.getElementById('one_time_download').checked
            );
    }
}
