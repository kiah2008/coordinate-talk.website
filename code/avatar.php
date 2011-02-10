<?php
/**
 * UCenter 应用程序开发 Example
 *
 * 设置头像的 Example 代码
 */

echo '
<img src="'.UC_API.'/avatar.php?uid='.$Cta_uid.'&size=big">
<img src="'.UC_API.'/avatar.php?uid='.$Cta_uid.'&size=middle">
<img src="'.UC_API.'/avatar.php?uid='.$Cta_uid.'&size=small">
<br><br>'.uc_avatar($Cta_uid);

?>