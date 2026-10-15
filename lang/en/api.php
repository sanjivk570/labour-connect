<?php

return [
    // OTP / Auth responses
    'otp_sent' => 'OTP sent successfully.',
    'otp_verified' => 'OTP verified successfully.',
    'otp_invalid' => 'The OTP you entered is invalid.',
    'otp_expired' => 'The OTP has expired. Please request a new one.',
    'otp_attempts_exceeded' => 'Too many invalid attempts. Please request a new OTP.',
    'login_success' => 'Logged in successfully.',
    'logout_success' => 'Logged out successfully.',
    'role_selected' => 'Role selected successfully.',
    'registration_completed' => 'Registration completed successfully.',
    'registration_step_saved' => 'Step saved successfully.',

    // Profile
    'profile_updated' => 'Profile updated successfully.',
    'profile_not_found' => 'Profile not found.',
    'availability_updated' => 'Availability updated successfully.',
    'unauthorized' => 'You are not authorized to perform this action.',
    'not_found' => 'The requested resource was not found.',

    // Validation messages (attribute-level overrides)
    'validation' => [
        'mobile' => [
            'required' => 'Please enter your mobile number.',
            'regex' => 'Please enter a valid 10-digit mobile number.',
        ],
        'otp' => [
            'required' => 'Please enter the OTP.',
            'digits' => 'OTP must be 6 digits.',
        ],
        'role' => [
            'required' => 'Please select a role.',
            'in' => 'The selected role is invalid.',
        ],
    ],
];
