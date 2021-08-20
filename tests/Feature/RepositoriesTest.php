<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RepositoriesTest extends TestCase
{

    use RefreshDatabase;
    /**
     *
     * @return void
     */

    public function testRepositoriesShouldThrowAnErrorIfUserNameNotExist()
    {
        $username = 'PerseuAndZeus';
        $this->get("api/repositories/{$username}")
            ->assertStatus(self::HTTP_OK)
            ->assertJsonStructure(['errors' => ['invalid']]);
    }

    public function testRepositoriesShouldThrowAnErrorIfUserNameEmpty()
    {
        $username = 'yficklis';
        $this->get("api/repositories/{$username}")
            ->assertStatus(self::HTTP_OK)
            ->assertJsonStructure(['errors'=> ['emptyStars']]);
    }

}
