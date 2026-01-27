<?php
echo "Testing URLs:<br><br>";
echo "Base URL: " . "http://localhost:8081/CredentiaTAU/" . "<br>";
echo "Login POST should go to: " . "http://localhost:8081/CredentiaTAU/login" . "<br><br>";

echo "Testing form submission...<br>";
?>
<form action="http://localhost:8081/CredentiaTAU/login" method="post">
    <input type="email" name="email" value="artryry6@gmail.com"><br>
    <input type="password" name="password" value="superadmin123"><br>
    <button type="submit">Test Submit</button>
</form>