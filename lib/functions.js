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

function upload_progress (e)
{
    if (!e.lengthComputable)
        return;
    /* Show the user the operation do not reach 100%, the server need time
     * to give a response before providing the link.
     */
    var p = Math.round (e.loaded * 99 / e.total);
    document.getElementById('uploaded_percentage').innerHTML = p.toString() + '%';
}

function upload_failed (e)
{
    /* Todo: Considere showing a error div. */
    alert ('Sorry, upload failed');
}

function upload (url, file, time, password, one_time)
{
    var req = new XMLHttpRequest ();
    req.upload.addEventListener("progress", upload_progress, false);
    req.addEventListener("error", upload_failed, false);
    req.addEventListener("abort", upload_failed, false);
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

function start_upload (url)
{
    upload (url,
            document.getElementById('file_select').files[0],
            document.getElementById('select_time').value,
            document.getElementById('input_key').value,
            document.getElementById('one_time_download').checked
            );
}