# Dados Antropométricos WHO 2007

Este diretório contém os arquivos Excel com os dados de referência da WHO 2007 para cálculos antropométricos.

## Arquivos Necessários

### **Arquivos Obrigatórios (Z-scores)**

#### 1. `bmi-boys-z-who-2007-exp.xlsx`
**Descrição**: Dados LMS e Z-scores para meninos (5-19 anos)  
**Estrutura esperada**:
```
Month | L      | M      | S      | SD4neg | SD3neg | SD2neg | SD1neg | SD0    | SD1    | SD2    | SD3    | SD4
61    | -1.000 | 16.123 | 0.0812 | 12.452 | 13.127 | 13.892 | 14.774 | 15.801 | 16.123 | 18.501 | 20.389 | 22.513
62    | -1.000 | 16.189 | 0.0815 | 12.498 | 13.178 | 13.948 | 14.836 | 15.871 | 16.189 | 18.591 | 20.495 | 22.638
...
```

#### 2. `bmi-girls-z-who-2007-exp.xlsx`
**Descrição**: Dados LMS e Z-scores para meninas (5-19 anos)  
**Estrutura esperada**:
```
Month | L      | M      | S      | SD4neg | SD3neg | SD2neg | SD1neg | SD0    | SD1    | SD2    | SD3    | SD4
61    | -1.000 | 16.034 | 0.0849 | 12.301 | 12.962 | 13.709 | 14.567 | 15.563 | 16.034 | 18.351 | 20.201 | 22.265
62    | -1.000 | 16.098 | 0.0852 | 12.344 | 13.009 | 13.761 | 14.625 | 15.628 | 16.098 | 18.432 | 20.295 | 22.375
...
```

### **Arquivos Opcionais (Percentis)**

#### 3. `bmi-boys-perc-who2007-exp.xlsx`
**Descrição**: Dados de percentis para meninos (5-19 anos)  
**Estrutura esperada**:
```
Month | L      | M      | S      | P01   | P1    | P3    | P5    | P10   | P15   | P25   | P50   | P75   | P85   | P90   | P95   | P97   | P99   | P999
61    | -1.000 | 16.123 | 0.0812 | 12.45 | 13.13 | 13.89 | 14.34 | 15.02 | 15.42 | 16.12 | 17.85 | 19.68 | 20.89 | 21.58 | 22.77 | 23.45 | 25.12 | 27.89
62    | -1.000 | 16.189 | 0.0815 | 12.50 | 13.18 | 13.95 | 14.40 | 15.08 | 15.48 | 16.19 | 17.92 | 19.76 | 20.98 | 21.67 | 22.87 | 23.55 | 25.23 | 28.01
...
```

#### 4. `bmi-girls-perc-who2007-exp.xlsx`
**Descrição**: Dados de percentis para meninas (5-19 anos)  
**Estrutura esperada**:
```
Month | L      | M      | S      | P01   | P1    | P3    | P5    | P10   | P15   | P25   | P50   | P75   | P85   | P90   | P95   | P97   | P99   | P999
61    | -1.000 | 16.034 | 0.0849 | 12.30 | 12.96 | 13.71 | 14.15 | 14.82 | 15.21 | 15.90 | 17.61 | 19.42 | 20.61 | 21.29 | 22.45 | 23.12 | 24.76 | 27.45
62    | -1.000 | 16.098 | 0.0852 | 12.34 | 13.01 | 13.76 | 14.20 | 14.87 | 15.26 | 15.95 | 17.67 | 19.49 | 20.68 | 21.37 | 22.53 | 23.21 | 24.86 | 27.56
...
```

## Como Obter os Dados

1. **Fonte oficial**: [WHO Growth Reference Data](https://www.who.int/tools/growth-reference-data-for-5to19-years)
2. **Download**: Baixe os arquivos "BMI-for-age z-scores" e "BMI-for-age percentiles" para meninos e meninas
3. **Formato**: Certifique-se de que estão no formato Excel (.xlsx)
4. **Localização**: Coloque os arquivos neste diretório com os nomes exatos mencionados acima

## Estrutura dos Dados

### Colunas Obrigatórias
- **Month**: Idade em meses (61-228, equivale a 5-19 anos)
- **L**: Parâmetro L da distribuição Box-Cox 
- **M**: Mediana (parâmetro M)
- **S**: Coeficiente de variação (parâmetro S)

### Colunas Opcionais (Desvios Padrão)
- **SD4neg** a **SD4**: Valores para -4 a +4 desvios padrão
- Úteis para validação e cálculos alternativos

### Colunas Opcionais (Percentis)
- **P01** a **P999**: Percentis de P0.1 a P99.9
- Úteis para validação e relatórios completos

## Carregamento dos Dados

Após colocar os arquivos neste diretório, execute:

```bash
php artisan migrate
php artisan anthropometric:load
```

Ou use o seeder diretamente:

```bash
php artisan db:seed --class=AnthropometricDataSeeder
```

## Verificação

Para verificar se os dados foram carregados corretamente:

```bash
php artisan tinker
>>> App\Models\AnthropometricLmsReference::count()
>>> App\Models\AnthropometricLmsReference::where('gender', 'M')->count()
>>> App\Models\AnthropometricLmsReference::where('gender', 'F')->count()
```

## Troubleshooting

### Arquivo não encontrado
- Verifique se o nome do arquivo está exato
- Certifique-se de que o arquivo está neste diretório
- Verifique se o arquivo não está corrompido

### Estrutura de dados incorreta
- Confirme se as colunas estão na ordem correta
- Verifique se a primeira linha contém os cabeçalhos
- Certifique-se de que não há linhas vazias no início

### Performance
- O carregamento pode demorar alguns minutos (são ~336 registros por arquivo)
- Use `--verbose` para ver o progresso: `php artisan anthropometric:load --verbose`

## Arquivos Esperados

Este diretório deve conter:

```
database/xls/anthro/
├── bmi-boys-z-who-2007-exp.xlsx      (obrigatório - Z-scores meninos)
├── bmi-girls-z-who-2007-exp.xlsx     (obrigatório - Z-scores meninas)  
├── bmi-boys-perc-who2007-exp.xlsx    (opcional - Percentis meninos)
└── bmi-girls-perc-who2007-exp.xlsx   (opcional - Percentis meninas)
```