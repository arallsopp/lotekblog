<?php
require_once('components.php');
$pageConstructor = new pageConstructor();
?>
<!DOCTYPE html>
<html lang="en">
<?php $pageConstructor->buildHead();?>
<body>
<?php
$pageConstructor->buildNav();
$pageConstructor->buildHeader();
$pageConstructor->buildContent();
$pageConstructor->buildFooter();
$pageConstructor->LinkScripts();
?>
</body>
</html>
