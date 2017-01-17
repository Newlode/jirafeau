
<div id="copyright">
    <p>
        <!-- Project links -->
        <?php
          echo t('Made with') .
            ' <a href="https://gitlab.com/mojo42/Jirafeau">' . t('Jirafeau Project') . '</a>' .
            ' (<a href="https://www.gnu.org/licenses/agpl.html"><abbr title="Affero General Public License">AGPL</abbr>v3</a>)';
        ?>
        <!-- Installation dependend links -->
        <?php
        if (false === empty($cfg['web_root']))
        {
          echo ' | ';
          echo '<a href="' . $cfg['web_root'] . 'tos.php">' . t('Terms of Service') . '</a>';
        }
        ?>
    </p>
</div>
</div>
<div id="jyraphe">
</div>
</body>
</html>
