<?php

namespace Tests\Browser;

use App\Models\LegacyInstitution;
use App\Models\LegacyUser;
use App\Models\State;
use Database\Factories\CityFactory;
use Database\Factories\CountryFactory;
use Database\Factories\DistrictFactory;
use Database\Factories\LegacyEmployeeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyPersonFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\StateFactory;
use Illuminate\Support\Facades\Hash;

/**
 * Preparação de estado compartilhada pelos testes de browser.
 *
 * Testes de browser passam por HTTP real, então não podem usar o
 * WithoutMiddleware dos testes de página: o estado tem que existir no banco.
 */
final class Scenario
{
    /**
     * Libera o acesso ao sistema.
     *
     * Sem a configuração geral gravada, o middleware Suspended redireciona
     * qualquer requisição para /intranet/suspenso.php.
     */
    public static function enableSystem(): void
    {
        LegacyGeneralConfigurationFactory::new()->create([
            'active_on_ieducar' => true,
        ]);
    }

    /**
     * Autentica pela interface e devolve a página resultante.
     *
     * O login é feito pela UI, e não com actingAs(), porque o actingAs não
     * atravessa para a requisição servida ao browser: o Pest monta uma nova
     * requisição a partir dos cookies, então a sessão precisa ser criada de
     * verdade pelo formulário.
     */
    public static function login(string $login, string $password)
    {
        return visit('/login')
            ->fill('login', $login)
            ->fill('password', $password)
            ->click('Entrar');
    }

    /**
     * Cria um administrador com credenciais e nome conhecidos.
     *
     * O login é a matrícula do funcionário e a senha fica em
     * portal.funcionario, com o hash do Laravel. Administrador porque o
     * Gate::before libera todos os processos da ACL legada nesse nível.
     *
     * O nome é fixado para que os testes possam afirmar quem está logado: é
     * ele que aparece no topo da intranet.
     */
    public static function createAdmin(string $login, string $password, string $name): LegacyUser
    {
        $individual = LegacyIndividualFactory::new()->create([
            'idpes' => LegacyPersonFactory::new()->create(['nome' => $name]),
        ]);

        LegacyEmployeeFactory::new()->create([
            'ref_cod_pessoa_fj' => $individual->idpes,
            'matricula' => $login,
            'senha' => Hash::make($password),
        ]);

        return LegacyUserFactory::new()->admin()->create([
            'cod_usuario' => $individual->idpes,
        ]);
    }

    /**
     * Cria a cadeia de endereçamento: um país, um estado, uma cidade e um
     * distrito.
     *
     * O formulário da instituição monta o <select> de UF a partir de
     * State::getListKeyAbbreviation(). Sem nenhum estado cadastrado a lista
     * fica só com "Selecione" e, por ser campo obrigatório, a validação do
     * clsCadastro aborta o submit: o clique em "Salvar" acontece, mas a página
     * não navega.
     *
     * A sigla é fixada em duas letras porque pmieducar.instituicao.ref_sigla_uf
     * é character(2), enquanto o StateFactory gera algo como "M C".
     */
    public static function createAddressing(string $abbreviation = 'SC'): State
    {
        $state = StateFactory::new()->create([
            'country_id' => CountryFactory::new()->create(),
            'abbreviation' => $abbreviation,
        ]);

        $city = CityFactory::new()->create([
            'state_id' => $state,
            'name' => 'Içara',
        ]);

        DistrictFactory::new()->create([
            'city_id' => $city,
        ]);

        return $state;
    }

    /**
     * Cria uma instituição com o endereço completo.
     *
     * A UF vem do estado cadastrado, para que o <select> do formulário
     * pré-selecione a opção certa, e o número é preenchido porque o factory o
     * deixa nulo.
     */
    public static function createInstitution(string $name, State $state): LegacyInstitution
    {
        return LegacyInstitutionFactory::new()->create([
            'nm_instituicao' => $name,
            'ref_sigla_uf' => $state->abbreviation,
            'numero' => 100,
        ]);
    }
}
