<?php 
    require 'controllers/common.php';

    class UserAuthenticationController {

        /**
         * @api {post} /register Register
         * @apiName register
         * @apiGroup User
         *
         * @apiParam {string} username Usenrame
         * @apiParam {string} password Password
         * @apiParam {string} mobile Mobile
         * @apiParam {string} privateKey Private Key
         * @apiParam {string} email Email
         *
         * @apiSuccess {string} status Status of the request.
         * @apiSuccess {string} message Message corresponding to request.
         */
        public static function register($request, $response, $args)  {
            
            $postdata = $request->getParams();   
            $isUsernamePresent = self::checkUsername($postdata['username']);
            
            if($isUsernamePresent == "PRESENT") {
                CommonController::sendResponseWithCode(500, 'OK', 'This username is already been taken. Please use different one.', $response);
            } else {
                $isEmailPresent = self::checkEmail($postdata['email']);
                if($isEmailPresent == "PRESENT") {
                    CommonController::sendResponseWithCode(500, 'OK', 'This email is already linked with another account. Pelase use different email.', $response);
                } else {
                    $rowdata = Model::factory('User')->create();
                    $date = date('Y-m-d H:i:s');
                    $rowdata->username = $postdata['username'];
                    $rowdata->password = $postdata['password'];
                    $rowdata->mobile = $postdata['mobile'];
                    $rowdata->private_key = $postdata['privateKey'];
                    $rowdata->email = $postdata['email'];
                    $rowdata->date = $date;
                    $res = $rowdata->save();
                    $userid = $rowdata->id();
                    if ($res) {
                        $objVerify = Model::factory('Verification')->create();
                        $objVerify->bluff_code = CommonController::generateBluffCode();
                        $objVerify->token = CommonController::generateToken();
                        $objVerify->type = 'SIGNUP';
                        $objVerify->user_id = $userid;
                        $res1 = $objVerify->save();
                        if($res1) {
                            $subject = 'Account Registration';
                            
                            $link = CommonController::getApiBaseUrl().'verify-email/'.$objVerify->bluff_code.'/'.$objVerify->token;

                            $message = file_get_contents("template/signup.html");                
                            $message = str_replace("USER_NAME", $rowdata->username, $message);
                            $message = str_replace("VERIFICATION_LINK", $link, $message);
                            
                            CommonController::sendEmail($rowdata->email, $subject, $message);
                            CommonController::sendResponseWithCode(200, 'OK', 'A verification email is sent to your email address. Please activate your account to get started.', $response);
                        }
                    } else{
                        CommonController::sendResponseWithCode(500, 'FAIL', 'Internal server error, Please try again later.', $response);
                    }
                }
            }
        }

        /**
         * @api {get} /verifyEmail Verify Email Address
         * @apiName verifyEmail
         * @apiGroup User
         *
         * @apiParam {string} bluffCode Bluff Code
         * @apiParam {string} token Token
         *
         * @apiSuccess {string} status Status of the request.
         * @apiSuccess {string} message Message corresponding to request.
         */
        public static function verifyEmail($request, $response, $args)  {
            
            $bluff = $args['bluffCode'];
            $token = $args['token'];

            $res = Model::factory('Verification')
                ->where('bluff_code', $bluff)
                ->where('token', $token)
                ->where('type', 'SIGNUP')
                ->find_array();
            if($res) {
                $updRes = Model::factory('User')->find_one($res[0]['user_id']);
                if($updRes) {
                    
                    $updRes->is_verified = 1;
                    $suc = $updRes->save();
                    if($suc) {
                        self::deleteVerificationCode($res[0]['id']);
                        return $response->withRedirect('/login');
                    }
                } else {
                    CommonController::sendResponseWithCode(500, 'FAIL', 'Internal server error, please try again later.', $response);   
                }
            } else {
                CommonController::sendResponseWithCode(500, 'FAIL', 'This token has expired, please generate new token and try again.', $response);
            }
        }

        /**
         * @api {post} /login Login
         * @apiName login
         * @apiGroup User
         *
         * @apiParam {string} username Usenrame
         * @apiParam {string} password Password
         *
         * @apiSuccess {string} status Status of the request.
         * @apiSuccess {string} message Message corresponding to request.
         * @apiSuccess {string} data Session Token .
         */
        public static function login($request, $response, $args)  {
            
            $postdata = $request->getParams();

            $res = Model::factory('User')
                ->where('username', $postdata['username'])
                ->where('password', $postdata['password'])
                ->find_array();

            if($res) {
                $token = self::createUserSession($res[0]['id']);
                if($token) {
                    CommonController::sendBodyResponseWithCode(200, 'OK', 'User logged in successfully.', $token, $response);
                } else {
                    CommonController::sendResponseWithCode(500, 'FAIL', 'Internal server error, please try again later.', $response);    
                }
            } else {
                CommonController::sendResponseWithCode(500, 'FAIL', 'Invalid username or password.', $response);
            }
        }

        /**
         * @api {get} /validate Validate Session Token
         * @apiName validate
         * @apiGroup User
         *
         * @apiParam {string} token Token
         *
         * @apiSuccess {string} status Status of the request.
         * @apiSuccess {string} message Message corresponding to request.
         */
        public function validate($request, $response, $args) {

            $token = $args['token'];
            $res = Model::factory('Verification')
                ->where('token', $token)
                ->where('type', 'SESSION')
                ->find_array();
            
            if($res) {
                $userId = $res[0]['user_id'];
                $updRes = self::getUserDetailsById($userId);
                CommonController::sendBoyResponseWithCode(200, 'OK', 'User is logged in', $updRes, $response);
            } else {
                CommonController::sendResponseWithCode(500, 'FAIL', 'Your session has expired. Please login again.', $response);
            }
        }

        /**
         * @api {get} /logout Logout
         * @apiName logout
         * @apiGroup User
         *
         * @apiParam {string} email Email
         *
         * @apiSuccess {string} status Status of the request.
         * @apiSuccess {string} message Message corresponding to request.
         */
        public function logout($request, $response, $args) {

            $user = self::getUserDetailsByEmail($args['email']);
            $userId = $user[0]['id'];

            $res = Model::factory('Verification')
                ->where('user_id', $userId)
                ->where('type', 'SESSION')
                ->find_array();
            if($res) {
                $user = self::deleteVerificationCode($res[0]['id']);
            }
            return $response->withRedirect('/login');
        }

        /**
         * @api {get} /resetPassword Reset Password
         * @apiName resetPassword
         * @apiGroup User
         *
         * @apiParam {string} email Email
         *
         * @apiSuccess {string} status Status of the request.
         * @apiSuccess {string} message Message corresponding to request.
         */
        public function resetPassword($request, $response, $args) {

            $user = self::getUserDetailsByEmail($args['email']);
            if($user) {

                $resetRes = Model::factory('Verification')
                ->where('user_id', $user[0]['id'])
                ->where('type', 'RESET_PASSWORD')
                ->find_array();

                if($resetRes) {
                    
                    $subject = 'Reset Password';                    
                    $link = CommonController::getBaseUrl().'reset-password/'.$resetRes[0]['bluff_code'].'/'.$resetRes[0]['token'];

                    $message = file_get_contents("template/forgot.html");
                    $message = str_replace("VERIFICATION_LINK", $link, $message);
                    
                    CommonController::sendEmail($args['email'], $subject, $message);
                    CommonController::sendResponseWithCode(200, 'OK', 'An email to reset your password is sent to your email address. Please follow the instruction in it to reset your password.', $response);
                } else {
                    $objVerify = Model::factory('Verification')->create();
                    $objVerify->bluff_code = CommonController::generateBluffCode();
                    $objVerify->token = CommonController::generateToken();
                    $objVerify->type = 'RESET_PASSWORD';
                    $objVerify->user_id = $user[0]['id'];
                    $res1 = $objVerify->save();
                    if($res1) {
                        $subject = 'Reset Password';
                        
                        $link = CommonController::getBaseUrl().'reset-password/'.$objVerify->bluff_code.'/'.$objVerify->token;

                        $message = file_get_contents("template/forgot.html");
                        $message = str_replace("VERIFICATION_LINK", $link, $message);
                        
                        CommonController::sendEmail($args['email'], $subject, $message);
                        CommonController::sendResponseWithCode(200, 'OK', 'An email to reset password is sent to your email address. Please follow the instruction in it to reset your password.', $response);
                    }                    
                }

            } else {
                CommonController::sendResponseWithCode(500, 'FAIL', 'This email address is not linked with any account in our database. Please correct and try again', $response);
            }
        }

        /**
         * @api {get} /verifyResetEmail Verify Reset Email Token
         * @apiName verifyResetEmail
         * @apiGroup User
         *
         * @apiParam {string} bluffCode Bluff Code
         * @apiParam {string} token Token
         *
         * @apiSuccess {string} status Status of the request.
         * @apiSuccess {string} message Message corresponding to request.
         */
        public static function verifyResetEmail($request, $response, $args)  {
            
            $bluff = $args['bluffCode'];
            $token = $args['token'];

            $res = Model::factory('Verification')
                ->where('bluff_code', $bluff)
                ->where('token', $token)
                ->where('type', 'RESET_PASSWORD')
                ->find_array();
            if($res) {
                $updRes = Model::factory('User')->find_one($res[0]['user_id']);
                if($updRes) {
                    CommonController::sendResponseWithCode(200, 'OK', 'Valid token.', $response);   
                } else {
                    CommonController::sendResponseWithCode(500, 'FAIL', 'Internal server error, please try again later.', $response);   
                }
            } else {
                CommonController::sendResponseWithCode(500, 'FAIL', 'This token has expired, please generate new token and try again.', $response);
            }
        }

        /**
         * @api {post} /changePassword Change Password By Reset Link
         * @apiName changePassword
         * @apiGroup User
         *
         * @apiParam {string} bluff_code Bluff Code
         * @apiParam {string} reset_token Reset Token
         * @apiParam {string} password Password
         *
         * @apiSuccess {string} status Status of the request.
         * @apiSuccess {string} message Message corresponding to request.
         */
        public static function changePassword($request, $response, $args)  {

            $postdata = $request->getParams();
            $bluff = $postdata['bluff_code'];
            $token = $postdata['reset_token'];
            $password = $postdata['password'];

            $res = Model::factory('Verification')
                ->where('bluff_code', $bluff)
                ->where('token', $token)
                ->where('type', 'RESET_PASSWORD')
                ->find_array();
            if($res) {
                $user = self::getUserDetailsById($res[0]['user_id']);
                $cpRes = self::changeUserPassword($user['email'], $password);
                if($cpRes == "OK") {

                    self::deleteVerificationCode($res[0]['id']);
                    CommonController::sendResponseWithCode(200, 'OK', 'Your password is updated successfully.', $response);
                } else {
                    CommonController::sendResponseWithCode(500, 'FAIL', 'Internal server error, please try again later.', $response);
                }
            } else {
                CommonController::sendResponseWithCode(500, 'FAIL', 'This token has expired, please generate new token and try again.', $response);
            }
        }

        /**
         * @api {post} /updatePassword Update User Account Password
         * @apiName updatePassword
         * @apiGroup User
         *
         * @apiParam {string} email Email
         * @apiParam {string} password Password
         *
         * @apiSuccess {string} status Status of the request.
         * @apiSuccess {string} message Message corresponding to request.
         */
        public static function updatePassword($request, $response, $args)  {

            $postdata = $request->getParams();
            $email = $postdata['email'];
            $password = $postdata['password'];

            $res = self::changeUserPassword($email, $password);
            if($res == "OK") {
                CommonController::sendResponseWithCode(200, 'OK', 'Your password is updated successfully.', $response);
            } else {
                CommonController::sendResponseWithCode(500, 'FAIL', 'Internal server error, please try again later.', $response);
            }
        }

        public function changeUserPassword($email, $password) {

            $user = self::getUserDetailsByEmail($email);
            $updRes = Model::factory('User')->find_one($user[0]['id']);

            if($updRes) {
                $updRes->password = $password;
                $suc = $updRes->save();
                if($suc) {
                    return 'OK';
                }
            } else {
                return 'FAIL';
            }
        }

        public function checkUsername($username) {
            $res = Model::factory('User')
                ->where('username', $username)
                ->find_array();
            if($res) return "PRESENT";
            else return "NOT_PRESENT";
        }

        public function checkEmail($email) {
            $res = Model::factory('User')
                ->where('email', $email)
                ->find_array();
            if($res) return "PRESENT";
            else return "NOT_PRESENT";
        }        

        public static function deleteVerificationCode($id)  {

            $ver = Model::factory('Verification')
                ->find_one($id);
            $ver->delete();
        }

        public function createUserSession($userid) {

            $delRes = Model::factory('Verification')
                ->where('user_id', $userid)
                ->where('type', 'SESSION')
                ->find_array();

            if($delRes) {
                self::deleteVerificationCode($delRes[0]['id']);
            }

            $token = CommonController::generateToken();
            $objVerify = Model::factory('Verification')->create();
            $objVerify->bluff_code = CommonController::generateBluffCode();
            $objVerify->token = $token;
            $objVerify->type = 'SESSION';
            $objVerify->user_id = $userid;
            $res1 = $objVerify->save();
            if($res1) return $token;
            else return '';
        }

        public function getUserDetailsById($id) {
            $res = Model::factory('User')
                ->select_many(array('username', 'email', 'mobile'))->find_one($id);
            if($res) return $res->as_array();
            else '';
            
        }

        public function getUserDetailsByEmail($email) {
            $res = Model::factory('User')
                ->where('email', $email)
                ->find_array();
            if($res) return $res;
            else return "";
            
        }
    }
?>