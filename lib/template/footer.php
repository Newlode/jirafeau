
<div id="copyright">
    <p>
        <br />
        <!-- Project links -->
        <a href="https://www.gnu.org/licenses/agpl.html"><abbr title="Affero General Public License">AGPL</abbr>v3</a> |
        <a href="https://gitlab.com/mojo42/Jirafeau"><?php echo t('Jirafeau Project') ?></a>
        <!-- Installation dependend links -->
        <?php
        if (false === empty($cfg['web_root']))
        {
          echo ' | ';
          echo '<a href="' . $cfg['web_root'] . '/tos.php">' . t('Term Of Service') . '</a>';
        }
        ?>
    </p>
</div>
</div>
<div id="jyraphe">
</div>
</body>
</html>
