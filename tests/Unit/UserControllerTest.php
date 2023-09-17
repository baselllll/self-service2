<?php

namespace Tests\Unit;

use Tests\TestCase;

class UserControllerTest extends TestCase
{


    public function testRedirectHomeAfterMainRoute(){
        $response = $this->get('/');
        $response->assertStatus(302);
        $response->assertRedirect(route('home'));
    }

    public function testSeeHome(){
        $response = $this->get('home');
        $response->assertStatus(200);
        $response->assertSee("Call For Service");
        $response->assertSee("Request For Relax");
    }

    public function testSeeLoginPage(){
        $response = $this->get('login');
        $response->assertStatus(200);
        $response->assertSee("Login");
    }


    public function testRedirectHomeAfterProfileEmployee(){
        $response = $this->get('profile-employee');
        $response->assertStatus(302);
        $response->assertRedirect(route('home'));
    }


    public function testRedirectHomeAfterLogout(){
        $response = $this->get('logout');;
        $response->assertStatus(302);
        $response->assertRedirect(route('home'));
    }

    public function testEmployeeLogin()
    {
        $response = $this->post('/add-service-detail', [
            'absence_attendance_type_id' => 1061,
            'start_date' => '17-may-2023',
            'end_date' => '18-may-2023',
            'person_id' => 22772,
            'occurrence' => 44,
            'comments' => 'testts',
        ]);
        $response->assertSee("Login");
    }
}
