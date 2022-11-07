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

class ResponsavelExport implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithMapping
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
        
        $row[] = $responsavel->name;
        $row[] = $responsavel->date_of_birth;
        $row[] = $responsavel->cpf;
        $row[] = $responsavel->gender;
        $row[] = $responsavel->estado_civil;
        $row[] = $responsavel->rg;
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
        $row[] = $responsavel->endereco.' '.$responsavel->numero_casa.' '.$responsavel->bairro.' '.$responsavel->cep.' '.$responsavel->complemento;
        $row[] = $responsavel->ddd.' '.$responsavel->telefone;
        $row[] = $responsavel->email_address;
        $row[] = $responsavel->bank_branch;
        $row[] = $responsavel->bank_account;
        $row[] = $responsavel->type_bank_account;
        
 
        return $row;
    }

    public function headings(): array
    {
        return [
            'Nome',
            'Nascimento',
            'CPF',
            'Sexo',
            'Estado Cívil',
            'RG',
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
            'Endereço Completo',
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
