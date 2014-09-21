<?php
//the functions below get a client's time offset to figure out their local date and time
//Code contributed by Luke from TNG Forums.

function get_client_time_offset( ){
if(!isset($_COOKIE['GMT_bias'])) {
?>
<script type="text/javascript">
var Cookies = {};
/***
* @name = string, name of cookie
* @value = string, value of cookie
* @days = int, number of days before cookie expires
***/
Cookies.create = function (name, value, days) {
if (days) {
var date = new Date();
date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
var expires = "; expires=" + date.toGMTString();
} else {
var expires = "";
}
document.cookie = name + "=" + value + expires + "; path=/";
this[name] = value;
}
var now = new Date();
Cookies.create("GMT_bias",now.getTimezoneOffset(),7);
window.location = "<?php echo $_SERVER['PHP_SELF'];?>";
</script>
<?php
}
return $_COOKIE['GMT_bias'];

}


//function get_local_date( )
//{
//return gmdate( 'Y-m-d' , ( time() - get_client_time_offset( ) * 60 ) );
//}
function get_local_time( )
{
return gmdate( 'Y-m-d H:i:s' , ( time() - get_client_time_offset( ) * 60 ) );
}
function get_local_date($format)
{
return gmdate( $format , ( time() - get_client_time_offset( ) * 60 ) );
}

// end of functions for client date and time.
?>
