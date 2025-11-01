<?php

namespace Tests\Unit\Traits;

use App\Models\LegacyUserType;
use App\Traits\HasNotificationUsers;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Database\Factories\MenuFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HasNotificationUsersTest extends TestCase
{
    use DatabaseTransactions;

    private object $instance;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an anonymous class that uses the trait
        $this->instance = new class {
            use HasNotificationUsers;
        };
    }

    // ========== MC/DC Tests for getUsers() ==========

    /**
     * TC1: A=true, B=false, C=false, D=true
     * User linked to school (A=true), not institutional level (B=false),
     * not admin (C=false), has menu for process (D=true)
     * Tests condition A independently
     */
    public function test_returns_user_linked_to_specific_school()
    {
        // Create school first
        $school = LegacySchoolFactory::new()->create();
        $process = 999888;
        
        // Create user type with level > INSTITUTIONAL (B=false) and != ADMIN (C=false)
        $userType = LegacyUserTypeFactory::new()->create([
            'nivel' => LegacyUserType::LEVEL_SCHOOLING, // Level 4 > 2 (institutional)
        ]);
        
        // Create user with menu for process (D=true)
        $user = LegacyUserFactory::new()
            ->state(['ref_cod_tipo_usuario' => $userType->cod_tipo_usuario])
            ->withAccess($process)
            ->create();
        
        // Link user to school (A=true)
        DB::table('pmieducar.escola_usuario')->insert([
            'ref_cod_usuario' => $user->cod_usuario,
            'ref_cod_escola' => $school->cod_escola,
        ]);

        $result = $this->instance->getUsers($process, $school->cod_escola);

        $this->assertGreaterThanOrEqual(1, count($result));
        $userIds = array_column($result, 'cod_usuario');
        $this->assertContains($user->cod_usuario, $userIds);
    }

    /**
     * TC2: A=false, B=true, C=false, D=true
     * User NOT linked to school (A=false), institutional level (B=true),
     * not admin (C=false), has menu for process (D=true)
     * Tests condition B independently
     */
    public function test_returns_user_with_institutional_level()
    {
        $school = 1;
        $process = 999889;
        
        // Create user type with institutional level (B=true) and != ADMIN (C=false)
        $userType = LegacyUserTypeFactory::new()->create([
            'nivel' => LegacyUserType::LEVEL_INSTITUTIONAL, // Level 2
        ]);
        
        // Create user with menu for process (D=true)
        $user = LegacyUserFactory::new()
            ->state(['ref_cod_tipo_usuario' => $userType->cod_tipo_usuario])
            ->withAccess($process)
            ->create();

        $result = $this->instance->getUsers($process, $school);

        $this->assertGreaterThanOrEqual(1, count($result));
        $userIds = array_column($result, 'cod_usuario');
        $this->assertContains($user->cod_usuario, $userIds);
    }

    /**
     * TC3: A=false, B=false, C=true, D=false
     * User NOT linked to school (A=false), not institutional level (B=false),
     * is admin (C=true), no menu for process (D=false)
     * Tests condition C independently
     */
    public function test_returns_admin_user_regardless_of_menu()
    {
        $school = 1;
        $process = 999890;
        
        // Create admin user (C=true, B=false - admin level is 1 < 2)
        $user = LegacyUserFactory::new()->admin()->create();

        $result = $this->instance->getUsers($process, $school);

        $this->assertGreaterThanOrEqual(1, count($result));
        $userIds = array_column($result, 'cod_usuario');
        $this->assertContains($user->cod_usuario, $userIds);
    }

    /**
     * TC4: A=false, B=true, C=false, D=true
     * User NOT linked to school (A=false), institutional level (B=true),
     * not admin (C=false), has menu for process (D=true)
     * Tests condition D independently
     */
    public function test_returns_user_with_menu_for_process()
    {
        $school = 1;
        $process = 999891;
        
        // Create user type with institutional level (B=true to pass first WHERE) and != admin (C=false)
        $userType = LegacyUserTypeFactory::new()->create([
            'nivel' => LegacyUserType::LEVEL_INSTITUTIONAL,
        ]);
        
        // Create user with menu for process (D=true)
        $user = LegacyUserFactory::new()
            ->state(['ref_cod_tipo_usuario' => $userType->cod_tipo_usuario])
            ->withAccess($process)
            ->create();

        $result = $this->instance->getUsers($process, $school);

        // Should return user: (A=false OR B=true) AND (C=false OR D=true) = true AND true = true
        $this->assertGreaterThanOrEqual(1, count($result));
        $userIds = array_column($result, 'cod_usuario');
        $this->assertContains($user->cod_usuario, $userIds);
    }

    /**
     * TC4b: Alternative test for D - A=true, B=false, C=false, D=true
     * User linked to school (A=true), not institutional level (B=false),
     * not admin (C=false), has menu for process (D=true)
     * Tests condition D with different first WHERE combination
     */
    public function test_returns_user_linked_to_school_with_menu()
    {
        // Create school first
        $school = LegacySchoolFactory::new()->create();
        $process = 999892;
        
        // Create user type > institutional and != admin (B=false, C=false)
        $userType = LegacyUserTypeFactory::new()->create([
            'nivel' => LegacyUserType::LEVEL_SCHOOLING,
        ]);
        
        // Create user with menu for process (D=true)
        $user = LegacyUserFactory::new()
            ->state(['ref_cod_tipo_usuario' => $userType->cod_tipo_usuario])
            ->withAccess($process)
            ->create();
        
        // Link to school (A=true)
        DB::table('pmieducar.escola_usuario')->insert([
            'ref_cod_usuario' => $user->cod_usuario,
            'ref_cod_escola' => $school->cod_escola,
        ]);

        $result = $this->instance->getUsers($process, $school->cod_escola);

        // Should return user: (A=true OR B=false) AND (C=false OR D=true) = true AND true = true
        $this->assertGreaterThanOrEqual(1, count($result));
        $userIds = array_column($result, 'cod_usuario');
        $this->assertContains($user->cod_usuario, $userIds);
    }

    /**
     * TC5: A=false, B=false, C=false, D=false
     * User NOT linked to school (A=false), not institutional level (B=false),
     * not admin (C=false), no menu for process (D=false)
     * All conditions false - should NOT return user
     */
    public function test_does_not_return_user_when_all_conditions_false()
    {
        $school = 1;
        $process = 999895;
        
        // Create user type > institutional and != admin (B=false, C=false)
        $userType = LegacyUserTypeFactory::new()->create([
            'nivel' => LegacyUserType::LEVEL_SCHOOLING,
        ]);
        
        // Create user WITHOUT menu for process (D=false)
        $user = LegacyUserFactory::new()
            ->state(['ref_cod_tipo_usuario' => $userType->cod_tipo_usuario])
            ->create();
        
        // NOT linked to school (A=false)

        $result = $this->instance->getUsers($process, $school);

        // Should NOT contain this user
        $userIds = array_column($result, 'cod_usuario');
        $this->assertNotContains($user->cod_usuario, $userIds);
    }
}
