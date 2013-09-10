<?php

class UserController extends BaseController {

	protected $sendgrid;

	/**
	 * Instantiate a new UserController
	 */
	public function __construct()
	{
		//Check CSRF token on POST
		$this->beforeFilter('csrf', array('on' => 'post'));
		
		//Enable the throttler.  [I am not sure about this...]
		// Get the Throttle Provider
		$throttleProvider = Sentry::getThrottleProvider();

		// Enable the Throttling Feature
		$throttleProvider->enable();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		// Index - show the user details.

		try
		{
		   	// Find the current user
		    if ( Sentry::check())
			{
			    // Find the user using the user id
			    $data['user'] = Sentry::getUser();

			    if ( $data['user']->hasAccess('admin')) {
			    	$data['allUsers'] = Sentry::getUserProvider()->findAll();

			    	//Assemble an array of each user's status
			    	$data['userStatus'] = array();
			    	foreach ($data['allUsers'] as $user) {
			    		if ($user->isActivated()) 
			    		{
			    			$data['userStatus'][$user->id] = trans('messages.activo');
			    		} 
			    		else 
			    		{
			    			$data['userStatus'][$user->id] = trans('messages.no activo');
			    		}

			    		//Pull Suspension & Ban info for this user
			    		$throttle = Sentry::getThrottleProvider()->findByUserId($user->id);

			    		//Check for suspension
			    		if($throttle->isSuspended())
					    {
					        // User is Suspended
					        $data['userStatus'][$user->id] = trans('messages.suspendido');
					    }

			    		//Check for ban
					    if($throttle->isBanned())
					    {
					        // User is Banned
					        $data['userStatus'][$user->id] = trans('messages.prohibido');
					    }

			    	}
			    } 

			    return View::make('users.index')->with($data);
			} else {
				Session::flash('error', trans('messages.No te has logueado'));
				return Redirect::to('/');
			}
		}
		catch (Cartalyst\Sentry\UserNotFoundException $e)
		{
		    Session::flash('error', trans('messages.Hubo un problema al entrar en tu cuenta'));
			return Redirect::to('/');
		}
	}

	/**
	 *  Display this user's details.
	 */
	
	public function getShow($id)
	{
		try
		{
		    //Get the current user's id.
			Sentry::check();
			$currentUser = Sentry::getUser();

		   	//Do they have admin access?
			if ( $currentUser->hasAccess('admin') || $currentUser->getId() == $id)
			{
				//Either they are an admin, or:
				//They are not an admin, but they are viewing their own profile.
				$data['user'] = Sentry::getUserProvider()->findById($id);
				$data['myGroups'] = $data['user']->getGroups();
				return View::make('users.show')->with($data);
			} else {
				Session::flash('error', trans('messages.No tienes acceso a ese usuario'));
				return Redirect::to('/');
			}

		}
		catch (Cartalyst\Sentry\UserNotFoundException $e)
		{
		    Session::flash('error', trans('messages.Hubo un problema al entrar en tu cuenta'));
			return Redirect::to('/');
		}
	}


	/**
	 * Register a new user. 
	 *
	 * @return Response
	 */
	public function getRegister()
	{
		// Show the register form
		return View::make('users.register');
	}

	public function postRegister() 
	{
	
		// Gather Sanitized Input
		$input = array(
			'email' => Input::get('email'),
			'password' => Input::get('password'),
			'password_confirmation' => Input::get('password_confirmation')
			);

		// Set Validation Rules
		$rules = array (
			'email' => 'required|min:4|max:32|email',
			'password' => 'required|min:6|confirmed',
			'password_confirmation' => 'required'
			);

		//Run input validation
		$v = Validator::make($input, $rules);

		if ($v->fails())
		{
			// Validation has failed
			return Redirect::to('users/register')->withErrors($v)->withInput();
		}
		else 
		{

			try {
				//Attempt to register the user. 
				$user = Sentry::register(array('email' => $input['email'], 'password' => $input['password']));

				//Get the activation code & prep data for email
				$data['activationCode'] = $user->GetActivationCode();
				$data['email'] = $input['email'];
				$data['userId'] = $user->getId();

				//send email with link to activate.
				Mail::send('emails.auth.welcome', $data, function($m) use($data)
				{
				    $m->to($data['email'])->subject(trans('messages.Bienvenido a'). ' ' .trans('messages.Nombre de aplicación'));
				});

				//success!
		    	Session::flash('success', trans('messages.Tu cuenta ha sido creada. Revisa tu correo para activarla'));
		    	return Redirect::to('/');

			}
			catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
			{
			    Session::flash('error', trans('messages.El campo login es requerido'));
			    return Redirect::to('users/register')->withErrors($v)->withInput();
			}
			catch (Cartalyst\Sentry\Users\UserExistsException $e)
			{
			    Session::flash('error', trans('messages.El usuario ya existe'));
			    return Redirect::to('users/register')->withErrors($v)->withInput();
			}

		}
	}

	/**
	 * Activate a new User
	 */
	public function getActivate($userId = null, $activationCode = null) {
		try 
		{
		    // Find the user
		    $user = Sentry::getUserProvider()->findById($userId);

		    // Attempt user activation
		    if ($user->attemptActivation($activationCode))
		    {
		        // User activation passed
		        
		    	//Add this person to the user group. 
		    	$userGroup = Sentry::getGroupProvider()->findById(1);
		    	$user->addGroup($userGroup);

		        Session::flash('success', trans('messages.Tu cuenta ha sido creada'));
				return Redirect::to('/');
		    }
		    else
		    {
		        // User activation failed
		        Session::flash('error', trans('messages.Hubo un problema activando esta cuenta. Por favor contacta al administrador del sistema.'));
				return Redirect::to('/');
		    }
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
		    Session::flash('error', trans('messages.El usuario no existe'));
			return Redirect::to('/');
		}
		catch (Cartalyst\SEntry\Users\UserAlreadyActivatedException $e)
		{
		    Session::flash('error', trans('messages.Tu ya has activado esta cuenta'));
			return Redirect::to('/');
		}


	}

	/**
	 * Login
	 *
	 * @return Response
	 */
	public function getLogin()
	{
		// Show the register form
		return View::make('users.login');
	}

	public function postLogin() 
	{
		// Gather Sanitized Input
		$input = array(
			'email' => Input::get('email'),
			'password' => Input::get('password'),
			'rememberMe' => Input::get('rememberMe')
			);

		// Set Validation Rules
		$rules = array (
			'email' => 'required|min:4|max:32|email',
			'password' => 'required|min:6'
			);

		//Run input validation
		$v = Validator::make($input, $rules);

		if ($v->fails())
		{
			// Validation has failed
			return Redirect::to('users/login')->withErrors($v)->withInput();
		}
		else 
		{
			try
			{
			    //Check for suspension or banned status
				$user = Sentry::getUserProvider()->findByLogin($input['email']);
				$throttle = Sentry::getThrottleProvider()->findByUserId($user->id);
			    $throttle->check();

			    // Set login credentials
			    $credentials = array(
			        'email'    => $input['email'],
			        'password' => $input['password']
			    );

			    // Try to authenticate the user
			    $user = Sentry::authenticate($credentials, $input['rememberMe']);

			}
			catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
			{
			    // Sometimes a user is found, however hashed credentials do
			    // not match. Therefore a user technically doesn't exist
			    // by those credentials. Check the error message returned
			    // for more information.
			    Session::flash('error', 'Invalid username or password.' );
				return Redirect::to('users/login')->withErrors($v)->withInput();
			}
			catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
			{
			    echo 'User not activated.';
			    Session::flash('error', trans('messages.Aún no has activado esta cuenta'));
				return Redirect::to('users/login')->withErrors($v)->withInput();
			}

			// The following is only required if throttle is enabled
			catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e)
			{
			    $time = $throttle->getSuspensionTime();
			    Session::flash('error', trans('messages.Tu cuenta ha sido suspendida', array('time' => $time)));
				return Redirect::to('users/login')->withErrors($v)->withInput();
			}
			catch (Cartalyst\Sentry\Throttling\UserBannedException $e)
			{
			    Session::flash('error', trans('messages.Se ha prohibido tu acceso al sistema'));
				return Redirect::to('users/login')->withErrors($v)->withInput();
			}

			//Login was succesful.  
			return Redirect::to('/');
		}
	}

	/**
	 * Logout
	 */
	
	public function getLogout() 
	{
		Sentry::logout();
		return Redirect::to('/');
	}


	

	/**
	 * Forgot Password / Reset
	 */
	public function getResetpassword() {
		// Show the change password
		return View::make('users.reset');
	}

	public function postResetpassword () {
		// Gather Sanitized Input
		$input = array(
			'email' => Input::get('email')
			);

		// Set Validation Rules
		$rules = array (
			'email' => 'required|min:4|max:32|email'
			);

		//Run input validation
		$v = Validator::make($input, $rules);

		if ($v->fails())
		{
			// Validation has failed
			return Redirect::to('users/resetpassword')->withErrors($v)->withInput();
		}
		else 
		{
			try
			{
			    $user      = Sentry::getUserProvider()->findByLogin($input['email']);
			    $data['resetCode'] = $user->getResetPasswordCode();
			    $data['userId'] = $user->getId();
			    $data['email'] = $input['email'];

			    // Email the reset code to the user
				Mail::send('emails.auth.reset', $data, function($m) use($data)
				{
				    $m->to($data['email'])->subject(trans('messages.Confirmación de reseteo de clave'). ' | '. trans('messages.Nombre de aplicación'));
				});

				Session::flash('success', trans('messages.Revisa tu correo por la información del reseteo de clave'));
			    return Redirect::to('/');

			}
			catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
			{
			    echo trans('messages.El usuario no existe');
			}
		}

	}


	/**
	 * Reset User's password
	 */
	public function getReset($userId = null, $resetCode = null) {
		try
		{
		    // Find the user
		    $user = Sentry::getUserProvider()->findById($userId);
		    $newPassword = $this->_generatePassword(8,8);

		    // Attempt to reset the user password
		    if ($user->attemptResetPassword($resetCode, $newPassword))
		    {
		        // Password reset passed
		        // 
		        // Email the reset code to the user

			    //Prepare New Password body
			    $data['newPassword'] = $newPassword;
			    $data['email'] = $user->getLogin();

			    Mail::send('emails.auth.newpassword', $data, function($m) use($data)
				{
				    $m->to($data['email'])->subject(trans('messages.Nueva información sobre tu clave'). ' | '. trans('messages.Nombre de aplicación'));
				});

				Session::flash('success', trans('messages.Tu clave ha cambiado, revisa tu correo por la nueva clave'));
			    return Redirect::to('/');
		        
		    }
		    else
		    {
		        // Password reset failed
		    	Session::flash('error', trans('messages.Hubo un problema. Por favor contacta al administrador del sistema.'));
			    return Redirect::to('users/resetpassword');
		    }
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
		    echo 'User does not exist.';
		}
	}


	public function getClearreset($userId = null) {
		try
		{
		    // Find the user
		    $user = Sentry::getUserProvider()->findById($userId);

		    // Clear the password reset code
		    $user->clearResetPassword();

		    echo "clear.";
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
		    echo trans('messages.El usuario no existe');
		}
	}


	/**
	 *  Edit / Update User Profile
	 */
	
	public function getEdit($id) 
	{
		try
		{
		    //Get the current user's id.
			Sentry::check();
			$currentUser = Sentry::getUser();

		   	//Do they have admin access?
			if ( $currentUser->hasAccess('admin'))
			{
				$data['user'] = Sentry::getUserProvider()->findById($id);
				$data['userGroups'] = $data['user']->getGroups();
				$data['allGroups'] = Sentry::getGroupProvider()->findAll();
				return View::make('users.edit')->with($data);
			} 
			elseif ($currentUser->getId() == $id)
			{
				//They are not an admin, but they are viewing their own profile.
				$data['user'] = Sentry::getUserProvider()->findById($id);
				$data['userGroups'] = $data['user']->getGroups();
				return View::make('users.edit')->with($data);
			} else {
				Session::flash('error', trans('messages.No tienes acceso a ese usuario'));
				return Redirect::to('/');
			}

		}
		catch (Cartalyst\Sentry\UserNotFoundException $e)
		{
		    Session::flash('error', trans('messages.Hubo un problema al entrar en tu cuenta'));
			return Redirect::to('/');
		}
	}


	public function postEdit($id) {
		// Gather Sanitized Input
		$input = array(
			'firstName' => Input::get('firstName'),
			'lastName' => Input::get('lastName')
			);

		// Set Validation Rules
		$rules = array (
			'firstName' => 'alpha',
			'lastName' => 'alpha',
			);

		//Run input validation
		$v = Validator::make($input, $rules);

		if ($v->fails())
		{
			// Validation has failed
			return Redirect::to('users/edit/' . $id)->withErrors($v)->withInput();
		}
		else 
		{
			try
			{
				//Get the current user's id.
				Sentry::check();
				$currentUser = Sentry::getUser();

			   	//Do they have admin access?
				if ( $currentUser->hasAccess('admin')  || $currentUser->getId() == $id)
				{
					// Either they are an admin, or they are changing their own password. 
					// Find the user using the user id
					$user = Sentry::getUserProvider()->findById($id);	
					
				    // Update the user details
				    $user->first_name = $input['firstName'];
				    $user->last_name = $input['lastName'];

				    // Update the user
				    if ($user->save())
				    {
				        // User information was updated
				        Session::flash('success', trans('messages.Perfil actualizado'));
						return Redirect::to('users/show/'. $id);
				    }
				    else
				    {
				        // User information was not updated
				        Session::flash('error', trans('messages.El perfil no se pudo actualizar'));
						return Redirect::to('users/edit/' . $id);
				    }

				} else {
					Session::flash('error', trans('messages.No tienes acceso a ese usuario'));
					return Redirect::to('/');
				}			   			    
			}
			catch (Cartalyst\Sentry\Users\UserExistsException $e)
			{
			    Session::flash('error', trans('messages.El usuario ya existe'));
				return Redirect::to('users/edit/' . $id);
			}
			catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
			{
			    Session::flash('error', trans('messages.El usuario no se encontró'));
				return Redirect::to('users/edit/' . $id);
			}
		}
	}

	/**
	 * Process changepassword form. 
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function postChangepassword($id) 
	{
		// Gather Sanitized Input
		$input = array(
			'oldPassword' => Input::get('oldPassword'),
			'newPassword' => Input::get('newPassword'),
			'newPassword_confirmation' => Input::get('newPassword_confirmation')
			);

		// Set Validation Rules
		$rules = array (
			'oldPassword' => 'required|min:6',
			'newPassword' => 'required|min:6|confirmed',
			'newPassword_confirmation' => 'required'
			);

		//Run input validation
		$v = Validator::make($input, $rules);

		if ($v->fails())
		{
			// Validation has failed
			return Redirect::to('users/edit/' . $id)->withErrors($v)->withInput();
		}
		else 
		{
			try
			{
			    
				//Get the current user's id.
				Sentry::check();
				$currentUser = Sentry::getUser();

			   	//Do they have admin access?
				if ( $currentUser->hasAccess('admin')  || $currentUser->getId() == $id)
				{
					// Either they are an admin, or they are changing their own password. 
					$user = Sentry::getUserProvider()->findById($id);	
					if ($user->checkHash($input['oldPassword'], $user->getPassword())) 
			    	{
				    	//The oldPassword matches the current password in the DB. Proceed.
				    	$user->password = $input['newPassword'];

				    	if ($user->save())
					    {
					        // User saved
					        Session::flash('success', trans('messages.Tu clave ha cambiado'));
							return Redirect::to('users/show/'. $id);
					    }
					    else
					    {
					        // User not saved
					        Session::flash('error', trans('messages.No se pudo cambiar tu clave'));
							return Redirect::to('users/edit/' . $id);
					    }
					} else {
						// The oldPassword did not match the password in the database. Abort. 
						Session::flash('error', trans('messages.No has provisto una clave correcta'));
						return Redirect::to('users/edit/' . $id);
					}
				} else {
					Session::flash('error', trans('messages.No tienes acceso a ese usuario'));
					return Redirect::to('/');
				}			   			    
			}
			catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
			{
			    Session::flash('error', 'Login field required.');
				return Redirect::to('users/edit/' . $id);
			}
			catch (Cartalyst\Sentry\Users\UserExistsException $e)
			{
			    Session::flash('error', 'User already exists.');
				return Redirect::to('users/edit/' . $id);
			}
			catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
			{
			    Session::flash('error', trans('messages.El usuario no se encontró'));
				return Redirect::to('users/edit/' . $id);
			}
		}
	}

	/**
	 * Process changes to user's group memberships
	 * @param  int 		$id The affected user's id
	 * @return [type]     [description]
	 */
	public function postUpdatememberships($id)
	{
		try 
		{
			//Get the current user's id.
			Sentry::check();
			$currentUser = Sentry::getUser();

		   	//Do they have admin access?
			if ( $currentUser->hasAccess('admin'))
			{
				$user = Sentry::getUserProvider()->findById($id);
				$allGroups = Sentry::getGroupProvider()->findAll();
				$permissions = Input::get('permissions');
				
				$statusMessage = '';
				foreach ($allGroups as $group) {
					
					if (isset($permissions[$group->id])) 
					{
						//The user should be added to this group
						if ($user->addGroup($group))
					    {
					        $statusMessage .= trans('messages.Agregado a ') . $group->name . "<br />";
					    }
					    else
					    {
					        $statusMessage .= trans('messages.No se ha podido agregar a ') . $group->name . "<br />";
					    }
					} else {
						// The user should be removed from this group
						if ($user->removeGroup($group))
					    {
					        $statusMessage .= trans('messages.Removido de ')  . $group->name . "<br />";
					    }
					    else
					    {
					        $statusMessage .= trans('messages.No pudo ser removido de ') . $group->name . "<br />";
					    }
					}

				}
				Session::flash('info', $statusMessage);
				return Redirect::to('users/show/'. $id);
			} 
			else 
			{
				Session::flash('error', trans('messages.No tienes acceso a ese usuario'));
				return Redirect::to('/');
			}
	
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
		    Session::flash('error', trans('messages.El usuario no se encontró'));
			return Redirect::to('users/edit/' . $id);
		}
		catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
		{
		    Session::flash('error', trans('messages.Intentando entrar a grupos no identificados') );
			return Redirect::to('users/edit/' . $id);
		}
	}


	/**
	 * Prepare the "Ban User" form
	 * @param  int $id The user id
	 * @return View     The "Ban Form" view
	 */
	public function getSuspend($id)
	{
		try
		{
		    //Get the current user's id.
			Sentry::check();
			$currentUser = Sentry::getUser();

		   	//Do they have admin access?
			if ( $currentUser->hasAccess('admin'))
			{
				$data['user'] = Sentry::getUserProvider()->findById($id);
				return View::make('users.suspend')->with($data);
			} else {
				Session::flash('error', trans('messages.No tiene permitido hacer eso.'));
				return Redirect::to('/');
			}

		}
		catch (Cartalyst\Sentry\UserNotFoundException $e)
		{
		    Session::flash('error', trans('messages.Hubo un problema al entrar en esa cuenta'));
			return Redirect::to('/users');
		}
	}

	public function postSuspend($id)
	{
		// Gather Sanitized Input
		$input = array(
			'suspendTime' => Input::get('suspendTime')
			);

		// Set Validation Rules
		$rules = array (
			'suspendTime' => 'required|numeric'
			);

		//Run input validation
		$v = Validator::make($input, $rules);

		if ($v->fails())
		{
			// Validation has failed
			return Redirect::to('users/suspend/' . $id)->withErrors($v)->withInput();
		}
		else 
		{
			try
			{
				//Prep for suspension
				$throttle = Sentry::getThrottleProvider()->findByUserId($id);

				//Set suspension time
				$throttle->setSuspensionTime($input['suspendTime']);

				// Suspend the user
    			$throttle->suspend();

    			//Done.  Return to users page.
    			Session::flash('success', trans('messages.El usuario ha sido suspendido por :tiempo minutos.', array('tiempo' => $input['suspendTime'])));
				return Redirect::to('users');

			}
			catch (Cartalyst\Sentry\UserNotFoundException $e)
			{
			    Session::flash('error', trans('messages.Hubo un problema al entrar en esa cuenta'));
				return Redirect::to('/users');
			}
		}
	}


	public function postDelete($id)
	{
		try
		{
		    // Find the user using the user id
		    $user = Sentry::getUserProvider()->findById($id);

		    // Delete the user
		    if ($user->delete())
		    {
		        // User was successfully deleted
		        Session::flash('success', trans('messages.El usuario ha sido borrado'));
				return Redirect::to('/users');
		    }
		    else
		    {
		        // There was a problem deleting the user
		        Session::flash('error', trans('messages.Hubo un problema borrando el usuario.'));
				return Redirect::to('/users');
		    }
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
		    Session::flash('error', trans('messages.Hubo un problema para entrar en la cuenta del(los) usuario(s).'));
			return Redirect::to('/users');
		}
	}

	/**
	 * Generate password - helper function
	 * From http://www.phpscribble.com/i4xzZu/Generate-random-passwords-of-given-length-and-strength
	 * 
	 */
	
	private function _generatePassword($length=9, $strength=4) {
		$vowels = 'aeiouy';
		$consonants = 'bcdfghjklmnpqrstvwxz';
		if ($strength & 1) {
			$consonants .= 'BCDFGHJKLMNPQRSTVWXZ';
		}
		if ($strength & 2) {
			$vowels .= "AEIOUY";
		}
		if ($strength & 4) {
			$consonants .= '23456789';
		}
		if ($strength & 8) {
			$consonants .= '@#$%';
		}
	 
		$password = '';
		$alt = time() % 2;
		for ($i = 0; $i < $length; $i++) {
			if ($alt == 1) {
				$password .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			} else {
				$password .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		return $password;
	}

}