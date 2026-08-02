<?php

namespace Tests\Feature;

use App\Models\AnswerOption;
use App\Models\Category;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionnaireApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_questions_do_not_expose_answer_keys(): void
    {
        $category = Category::create(['name' => 'Economy', 'slug' => 'economy']);
        Question::create([
            'category_id' => $category->id,
            'text' => 'A public question',
            'answer_type' => 'agreement',
            'correct_answer' => 5,
        ]);
        AnswerOption::create(['type' => 'agreement', 'value' => 1, 'label' => 'Disagree']);

        $response = $this->getJson('/api/v1/questions/economy');

        $response->assertOk()
            ->assertJsonPath('total_questions', 1)
            ->assertJsonMissingPath('questions.0.correct_answer')
            ->assertJsonPath('questions.0.category_name', 'Economy');
    }

    public function test_admin_routes_require_a_bearer_token(): void
    {
        $this->getJson('/api/v1/admin/categories/list')
            ->assertUnauthorized();
    }

    public function test_an_admin_can_log_in_and_access_protected_routes(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'secret-password',
            'is_admin' => true,
        ]);

        $login = $this->postJson('/api/v1/admin/login', [
            'email' => $admin->email,
            'password' => 'secret-password',
            'device_name' => 'test-suite',
        ])->assertOk()->assertJsonPath('token_type', 'Bearer');

        $this->withToken($login->json('token'))
            ->getJson('/api/v1/admin/categories/list')
            ->assertOk()
            ->assertJsonPath('success', true);
    }
}
