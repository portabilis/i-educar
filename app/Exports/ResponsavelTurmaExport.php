<?php

namespace App\Exports;

use App\Models\GuardianType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ResponsavelTurmaExport implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithMapping
{
    protected $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function map($responsavel): array
    {
        $row = [];
        if($responsavel->tipo_responsavel=='r'){
            $row[] = $responsavel->name;
            $row[] = $responsavel->nome_aluno;
            $row[] = $responsavel->date_of_birth;
            $row[] = $responsavel->cpf;
            $row[] = $responsavel->gender;
            $row[] = $responsavel->estado_civil;
            $row[] = $responsavel->rg;
            $row[] = $responsavel->rg_issue_date;
            $row[] = $responsavel->orgao_exp_rg;
            $row[] = $responsavel->rg_state_abbreviation;
            $row[] = $responsavel->nis;
            $row[] = $responsavel->sus_number;
            $row[] = $responsavel->certidao;
            $row[] = $responsavel->estado_emissao_cn;
            $row[] = $responsavel->cartorio_emissao;
            $row[] = $responsavel->data_exp_certidao;
            $row[] = $responsavel->titulo;
            $row[] = $responsavel->zona;
            $row[] = $responsavel->secao;
            $row[] = $responsavel->nationality;
            $row[] = $responsavel->naturalidade;
            $row[] = $responsavel->profession;
            $row[] = $responsavel->complemento.", ".$responsavel->endereco.', '.$responsavel->numero_casa;
            $row[] = $responsavel->bairro;
            $row[] = $responsavel->cep;
            $row[] = $responsavel->ddd.' '.$responsavel->telefone;
            $row[] = $responsavel->email_address;
            $row[] = $responsavel->bank_branch;
            $row[] = $responsavel->bank_account;
            $row[] = $responsavel->type_bank_account;
              

        }elseif($responsavel->tipo_responsavel=='m'){

            $row[] = $responsavel->name_mae;
            $row[] = $responsavel->nome_aluno;
            $row[] = $responsavel->date_of_birth_mae;
            $row[] = $responsavel->cpf_mae;
            $row[] = $responsavel->gender_mae;
            $row[] = $responsavel->estado_civil_mae;
            $row[] = $responsavel->rg_mae;
            $row[] = $responsavel->rg_issue_date_mae;
            $row[] = $responsavel->orgao_exp_rg_mae;
            $row[] = $responsavel->rg_state_abbreviation_mae;
            $row[] = $responsavel->nis_mae;
            $row[] = $responsavel->sus_number_mae;
            $row[] = $responsavel->certidao_mae;
            $row[] = $responsavel->estado_emissao_cn_mae;
            $row[] = $responsavel->cartorio_emissao_mae;
            $row[] = $responsavel->data_exp_certidao_mae;
            $row[] = $responsavel->titulo_mae;
            $row[] = $responsavel->zona_mae;
            $row[] = $responsavel->secao_mae;
            $row[] = $responsavel->nationality_mae;
            $row[] = $responsavel->naturalidade_mae;
            $row[] = $responsavel->profession_mae;
            $row[] = $responsavel->complemento_mae.", ".$responsavel->endereco_mae.', '.$responsavel->numero_casa_mae;
            $row[] = $responsavel->bairro_mae;
            $row[] = $responsavel->cep_mae;
            $row[] = $responsavel->ddd_mae.' '.$responsavel->telefone_mae;
            $row[] = $responsavel->email_address_mae;
            $row[] = $responsavel->bank_branch_mae;
            $row[] = $responsavel->bank_account_mae;
            $row[] = $responsavel->type_bank_account_mae;  
  
        }elseif($responsavel->tipo_responsavel=='p'){
            
            $row[] = $responsavel->name_pai;
            $row[] = $responsavel->nome_aluno;
            $row[] = $responsavel->date_of_birth_pai;
            $row[] = $responsavel->cpf_pai;
            $row[] = $responsavel->gender_pai;
            $row[] = $responsavel->estado_civil_pai;
            $row[] = $responsavel->rg_pai;
            $row[] = $responsavel->rg_issue_date_pai;
            $row[] = $responsavel->orgao_exp_rg_pai;
            $row[] = $responsavel->rg_state_abbreviation_pai;
            $row[] = $responsavel->nis_pai;
            $row[] = $responsavel->sus_number_pai;
            $row[] = $responsavel->certidao_pai;
            $row[] = $responsavel->estado_emissao_cn_pai;
            $row[] = $responsavel->cartorio_emissao_pai;
            $row[] = $responsavel->data_exp_certidao_pai;
            $row[] = $responsavel->titulo_pai;
            $row[] = $responsavel->zona_pai;
            $row[] = $responsavel->secao_pai;
            $row[] = $responsavel->nationality_pai;
            $row[] = $responsavel->naturalidade_pai;
            $row[] = $responsavel->profession_pai;
            $row[] = $responsavel->complemento_pai.", ".$responsavel->endereco_pai.' '.$responsavel->numero_casa_pai;
            $row[] = $responsavel->bairro_pai;
            $row[] = $responsavel->cep_pai;
            $row[] = $responsavel->ddd_pai.' '.$responsavel->telefone_pai;
            $row[] = $responsavel->email_address_pai;
            $row[] = $responsavel->bank_branch_pai;
            $row[] = $responsavel->bank_account_pai;
            $row[] = $responsavel->type_bank_account_pai;  

        }elseif($responsavel->tipo_responsavel=='a'){

            $row[] = $responsavel->name_pai.", ".$responsavel->name_mae;
            $row[] = $responsavel->nome_aluno;
            $row[] = $responsavel->date_of_birth_pai.", ".$responsavel->date_of_birth_mae;
            $row[] = $responsavel->cpf_pai.", ".$responsavel->cpf_mae;
            $row[] = $responsavel->gender_pai.", ".$responsavel->gender_mae;
            $row[] = $responsavel->estado_civil_pai.", ".$responsavel->estado_civil_mae;
            $row[] = $responsavel->rg_pai.", ".$responsavel->rg_mae;
            $row[] = $responsavel->rg_issue_date_pai.", ".$responsavel->rg_issue_date_mae;
            $row[] = $responsavel->orgao_exp_rg_pai.", ".$responsavel->rg_issue_date_mae;
            $row[] = $responsavel->rg_state_abbreviation_pai.", ".$responsavel->rg_issue_date_mae;
            $row[] = $responsavel->nis_pai.", ".$responsavel->nis_mae;
            $row[] = $responsavel->sus_number_pai.", ".$responsavel->sus_number_mae;
            $row[] = $responsavel->certidao_pai.", ".$responsavel->certidao_mae;
            $row[] = $responsavel->estado_emissao_cn_pai.", ".$responsavel->estado_emissao_cn_mae;
            $row[] = $responsavel->cartorio_emissao_pai.", ".$responsavel->cartorio_emissao_mae;
            $row[] = $responsavel->data_exp_certidao_pai.", ".$responsavel->data_exp_certidao_mae;
            $row[] = $responsavel->titulo_pai.", ".$responsavel->titulo_mae;
            $row[] = $responsavel->zona_pai.", ".$responsavel->zona_mae;
            $row[] = $responsavel->secao_pai.", ".$responsavel->secao_mae;
            $row[] = $responsavel->nationality_pai.", ".$responsavel->nationality_mae;
            $row[] = $responsavel->naturalidade_pai.", ".$responsavel->naturalidade_mae;
            $row[] = $responsavel->profession_pai.", ".$responsavel->profession_mae;
            $row[] = $responsavel->complemento_pai.", ".$responsavel->endereco_pai.' '.$responsavel->numero_casa_pai;
            $row[] = $responsavel->bairro_pai;
            $row[] = $responsavel->cep_pai;
            $row[] = $responsavel->ddd_pai.' '.$responsavel->telefone_pai.", ".$responsavel->ddd_mae.' '.$responsavel->telefone_mae;
            $row[] = $responsavel->email_address_pai.", ".$responsavel->email_address_mae;
            $row[] = $responsavel->bank_branch_pai.", ".$responsavel->bank_branch_mae;
            $row[] = $responsavel->bank_account_pai.", ".$responsavel->bank_account_mae;
            $row[] = $responsavel->type_bank_account_pai.", ".$responsavel->type_bank_account_mae;  

        }elseif(empty($responsavel->tipo_responsavel)){
            if(!empty($responsavel->name_pai)){

                $row[] = $responsavel->name_pai;
                $row[] = $responsavel->nome_aluno;
                $row[] = $responsavel->date_of_birth_pai;
                $row[] = $responsavel->cpf_pai;
                $row[] = $responsavel->gender_pai;
                $row[] = $responsavel->estado_civil_pai;
                $row[] = $responsavel->rg_pai;
                $row[] = $responsavel->rg_issue_date_pai;
                $row[] = $responsavel->orgao_exp_rg_pai;
                $row[] = $responsavel->rg_state_abbreviation_pai;
                $row[] = $responsavel->nis_pai;
                $row[] = $responsavel->sus_number_pai;
                $row[] = $responsavel->certidao_pai;
                $row[] = $responsavel->estado_emissao_cn_pai;
                $row[] = $responsavel->cartorio_emissao_pai;
                $row[] = $responsavel->data_exp_certidao_pai;
                $row[] = $responsavel->titulo_pai;
                $row[] = $responsavel->zona_pai;
                $row[] = $responsavel->secao_pai;
                $row[] = $responsavel->nationality_pai;
                $row[] = $responsavel->naturalidade_pai;
                $row[] = $responsavel->profession_pai;
                $row[] = $responsavel->complemento_pai.", ".$responsavel->endereco_pai.' '.$responsavel->numero_casa_pai;
                $row[] = $responsavel->bairro_pai;
                $row[] = $responsavel->cep_pai;
                $row[] = $responsavel->ddd_pai.' '.$responsavel->telefone_pai;
                $row[] = $responsavel->email_address_pai;
                $row[] = $responsavel->bank_branch_pai;
                $row[] = $responsavel->bank_account_pai;
                $row[] = $responsavel->type_bank_account_pai;  

            }else{
                
            $row[] = $responsavel->name_mae;
            $row[] = $responsavel->nome_aluno;
            $row[] = $responsavel->date_of_birth_mae;
            $row[] = $responsavel->cpf_mae;
            $row[] = $responsavel->gender_mae;
            $row[] = $responsavel->estado_civil_mae;
            $row[] = $responsavel->rg_mae;
            $row[] = $responsavel->rg_issue_date_mae;
            $row[] = $responsavel->orgao_exp_rg_mae;
            $row[] = $responsavel->rg_state_abbreviation_mae;
            $row[] = $responsavel->nis_mae;
            $row[] = $responsavel->sus_number_mae;
            $row[] = $responsavel->certidao_mae;
            $row[] = $responsavel->estado_emissao_cn_mae;
            $row[] = $responsavel->cartorio_emissao_mae;
            $row[] = $responsavel->data_exp_certidao_mae;
            $row[] = $responsavel->titulo_mae;
            $row[] = $responsavel->zona_mae;
            $row[] = $responsavel->secao_mae;
            $row[] = $responsavel->nationality_mae;
            $row[] = $responsavel->naturalidade_mae;
            $row[] = $responsavel->profession_mae;
            $row[] = $responsavel->complemento_mae.", ".$responsavel->endereco_mae.', '.$responsavel->numero_casa_mae;
            $row[] = $responsavel->bairro_mae;
            $row[] = $responsavel->cep_mae;
            $row[] = $responsavel->ddd_mae.' '.$responsavel->telefone_mae;
            $row[] = $responsavel->email_address_mae;
            $row[] = $responsavel->bank_branch_mae;
            $row[] = $responsavel->bank_account_mae;
            $row[] = $responsavel->type_bank_account_mae;  
            }
              

        } 
        
 
        return $row;
    }

    public function headings(): array
    {
        return [
            'Nome',
            'Responsável por',
            'Nascimento',
            'CPF',
            'Sexo',
            'Estado Cívil',
            'RG',
            'Data Exp. RG',
            'Órg Em. RG',
            'Estado Em. RG',
            'NIS',
            'Carteira do SUS',
            'Certidão de nascimento',
            'Estado Emissão CN',
            'Cartório Emissão',
            'Data Exp.Certidão',
            'Título de Eleitor',
            'Zona',
            'Seção',
            'Nacionalidade',
            'Naturalidade',
            'Profissão',
            'Endereço',
            'Bairro',
            'CEP',
            'Telefones',
            'Email',
            'Agência',
            'Conta',
            'Tipo de Conta',
            
           
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
