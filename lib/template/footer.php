
<div id="copyright">
    <p>
        <!-- Project links -->
        <?php
          echo t('Made with') .
            ' <a href="https://gitlab.com/mojo42/Jirafeau">' . t('Jirafeau Project') . '</a>' .
            ' (<a href="https://www.gnu.org/licenses/agpl.html"><abbr title="GNU Affero General Public License v3">AGPL-3.0</abbr></a>)';
        ?>
        <!-- Installation dependend links -->
        <?php
        if (true == $cfg['installation_done'])
        {
          echo ' | ';
          echo '<a href="' . JIRAFEAU_ABSPREFIX . 'tos.php">' . t('Terms of Service') . '</a>';
        }
        ?>
    </p>
</div>
</div>
<div id="jyraphe">
</div>
</body>
</html>
