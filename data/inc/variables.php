<?php
// SITE SPECIFIC VARIABLES ARE DEFINED HERE
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "root");
define("DB_NAME", "nascarpool_development");
/*define("DB_HOST", "mysql.hoggworks.com");
define("DB_USER", "boiler_user");
define("DB_PASS", "boiler_user_p4ss");
define("DB_NAME", "boilerplate_development");*/

define("SHOW_ERRORS", 0);               // controls whether errors are shown on the page. 
define("SERVER_URL", "http://localhost:8888/boilerplate/#/");
define("SITE_NAME", "Boilerplate");

// set table names

define("AUTHCODE_LENGTH", 255);
define("AUTHCODE_LIFESPAN", 604800);    // in seconds
define("AUTHCODE_CUTOFF", 3600);        // in seconds; if user comes to the site this near to expiry of the token, the token is expired
define("PUSH_AUTHCODE_LIFESPAN", true); // if true, every time a user checks validity of token, the lifespan is set to 
                                        // now + lifespan, so that the authcode will never expire if used frequently 
                                        // enough. This should also prevent any logging out of a session while actively 
                                        // using it, no matter how mong in order to try to prevent expiration 
                                        // happening mid-session
define("PERMISSIONS_USER", 0);
define("PERMISSIONS_ADMIN", 1);

define("DEFAULT_PERMISSIONS", PERMISSIONS_USER);
define("UNAUTHORIZED", "401");          // message that gets returned when a user without authorization is attempting 
                                        // to get access to something

define("REPLY_TO", "brian@hoggworks.com");

// messaging
define("LOGIN_ERROR_BAD_PASSWORD", "Bad Password");
define("LOGIN_ERROR_BAD_USER", "Unable to find the specified user");
define("LOGIN_ERROR_NO_DATA", "No login credentials were provided.");
define("LOGOUT_ERROR_GENERIC", "Unable to log you out.");
define("LOGOUT_ERROR_NO_AUTHCODE", "Unable to log you out: no auth code provided.");
define("REGISTER_ERROR_DB", "Unable to complete registration.");
define("REGISTER_NO_DATA", "No user information provided.");
define("RECOVER_NO_EMAIL", "No email address provided.");
define("RECOVER_EMAIL_NOT_FOUND", "The email address provided is not in the db.");
define("RECOVER_EMAIL_ERROR", "Unable to send your recovery email. ");
define("RECOVER_PASSWORD_CODE_LENGTH", 40);
define("RECOVER_PASSWORD_CUTOFF", 604800);   // in seconds; how long the password recovery is valid for
define("RECOVER_CANT_SEND_EMAIL", "We were unable to send your recovery password. Please try again.");
define("RECOVER_EMAIL_DB_ERROR", "There was an error recovering your password.");
define("RESET_PASSWORD_NO_PASSWORD", "Unable to reset password; none provided.");
define("RESET_NO_USER", "Unable to reset password; we couldn't locate your user information.");
define("RESET_DB_ERROR", "Unable to reset password; there was a db error.");
define("EMAIL_DIRECTORY", "../email/");
define("EMAIL_TEMPLATE_SUFFIX", ".html");

define("NO_AUTHCODE", "No authcode found");
define("EXPIRED_AUTHCODE", "The provided authcode has expired");
define("EMAIL_TEMPLATE_NOT_FOUND", "The specified email template could not be found");
define("EMAIL_SEND_FAILURE", "The requested email could not be sent.");

define("VALID_CHARS", "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.");  // Default character set for random strings


// email subjects
define("PASSWORD_RECOVERY_SUBJECT", "Recover your password");

// logging variables
define("EMAIL_ON_INFO", false);                                 // send email on 'info' log?
define("EMAIL_ON_WARN", false);                                 // send email on 'warn' log?
define("EMAIL_ON_ERROR", true);                                 // send email on 'error' log?
define("LOG_EMAIL_ADDRESES", ["brian@hoggworks.com"]);          // this is an arrary of email addresses
                                                                // if a log-related email is sent out, it 
                                                                // goes to every email in the array

?>