<?php

namespace Database\Seeders;

use App\Models\Container;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['name' => 'Shenzhen FastMake Co. Ltd',     'country' => 'China',       'contact_email' => 'sales@fastmake.cn',     'contact_phone' => '+86 755 1234567'],
            ['name' => 'Vietnam Production Group',       'country' => 'Vietnam',     'contact_email' => 'export@vpg.vn',         'contact_phone' => '+84 28 987654'],
            ['name' => 'Bangladesh Garments International','country'=>'Bangladesh',  'contact_email' => 'bgi@bgi-bd.com',        'contact_phone' => '+880 2 3456789'],
            ['name' => 'Turkey Textile Exports',         'country' => 'Turkey',      'contact_email' => 'export@tte.com.tr',     'contact_phone' => '+90 212 456789'],
        ];

        $createdSuppliers = [];
        foreach ($suppliers as $s) {
            $createdSuppliers[] = Supplier::create($s);
        }

        $contractsData = [
            ['supplier' => 0, 'number' => 'PO-2024-001', 'incoterms' => 'FOB', 'currency' => 'USD', 'value' => 125000.00, 'date' => '2024-01-15'],
            ['supplier' => 0, 'number' => 'PO-2024-002', 'incoterms' => 'CIF', 'currency' => 'USD', 'value' => 87500.00,  'date' => '2024-02-20'],
            ['supplier' => 1, 'number' => 'PO-2024-003', 'incoterms' => 'FOB', 'currency' => 'USD', 'value' => 54000.00,  'date' => '2024-03-10'],
            ['supplier' => 2, 'number' => 'PO-2024-004', 'incoterms' => 'EXW', 'currency' => 'EUR', 'value' => 32000.00,  'date' => '2024-03-25'],
            ['supplier' => 3, 'number' => 'PO-2024-005', 'incoterms' => 'CIF', 'currency' => 'EUR', 'value' => 68000.00,  'date' => '2024-04-05'],
        ];

        $contracts = [];
        foreach ($contractsData as $cd) {
            $contracts[] = Contract::create([
                'contract_number' => $cd['number'],
                'supplier_id'     => $createdSuppliers[$cd['supplier']]->id,
                'contract_date'   => $cd['date'],
                'incoterms'       => $cd['incoterms'],
                'currency'        => $cd['currency'],
                'total_value'     => $cd['value'],
            ]);
        }

        $containersData = [
            ['contract' => 0, 'number' => 'TCKU3456789', 'status' => 'in_transit',      'vessel' => 'MSC Beatrice',      'voyage' => 'BX123W', 'etd' => '2024-04-10', 'eta' => '2024-05-25', 'pol' => 'Shanghai', 'pod' => 'Genova'],
            ['contract' => 0, 'number' => 'MSCU7812340', 'status' => 'at_port',          'vessel' => 'MSC Beatrice',      'voyage' => 'BX123W', 'etd' => '2024-04-10', 'eta' => '2024-05-25', 'pol' => 'Shanghai', 'pod' => 'Genova'],
            ['contract' => 1, 'number' => 'OOLU5543210', 'status' => 'customs_cleared',  'vessel' => 'CSCL Globe',        'voyage' => 'GL044E', 'etd' => '2024-03-01', 'eta' => '2024-04-15', 'pol' => 'Ningbo',   'pod' => 'La Spezia'],
            ['contract' => 2, 'number' => 'APMU1234567', 'status' => 'in_production',    'vessel' => null,                'voyage' => null,     'etd' => null,         'eta' => '2024-06-30', 'pol' => 'Ho Chi Minh City', 'pod' => 'Napoli'],
            ['contract' => 3, 'number' => 'BICU9876543', 'status' => 'at_warehouse',     'vessel' => 'Maersk Skarstind', 'voyage' => 'SK088W', 'etd' => '2024-02-15', 'eta' => '2024-03-30', 'pol' => 'Chittagong', 'pod' => 'Venezia'],
            ['contract' => 4, 'number' => 'HLXU4321098', 'status' => 'in_transit',       'vessel' => 'Hapag Leipzig',    'voyage' => 'HL055E', 'etd' => '2024-04-20', 'eta' => '2024-05-28', 'pol' => 'Istanbul',   'pod' => 'Trieste'],
        ];

        foreach ($containersData as $cd) {
            Container::create([
                'contract_id'       => $contracts[$cd['contract']]->id,
                'container_number'  => $cd['number'],
                'status'            => $cd['status'],
                'vessel_name'       => $cd['vessel'],
                'voyage_number'     => $cd['voyage'],
                'etd'               => $cd['etd'],
                'eta'               => $cd['eta'],
                'port_of_loading'   => $cd['pol'],
                'port_of_discharge' => $cd['pod'],
            ]);
        }

        $paymentsData = [
            ['contract' => 0, 'desc' => '30% Acconto',         'amount' => 37500,   'pct' => 30, 'due' => '2024-01-20', 'paid' => '2024-01-19', 'ref' => 'WIRE-2024-0119', 'status' => 'paid'],
            ['contract' => 0, 'desc' => '70% a Vista BL',      'amount' => 87500,   'pct' => 70, 'due' => '2024-05-20', 'paid' => null,         'ref' => null,             'status' => 'pending'],
            ['contract' => 1, 'desc' => '30% Acconto',         'amount' => 26250,   'pct' => 30, 'due' => '2024-02-25', 'paid' => '2024-02-24', 'ref' => 'WIRE-2024-0224', 'status' => 'paid'],
            ['contract' => 1, 'desc' => '70% a Vista BL',      'amount' => 61250,   'pct' => 70, 'due' => '2024-04-10', 'paid' => null,         'ref' => null,             'status' => 'overdue'],
            ['contract' => 2, 'desc' => '100% a 60gg FOB',     'amount' => 54000,   'pct' => 100,'due' => now()->addDays(5)->format('Y-m-d'), 'paid' => null, 'ref' => null, 'status' => 'pending'],
            ['contract' => 3, 'desc' => '50% Acconto',         'amount' => 16000,   'pct' => 50, 'due' => '2024-03-30', 'paid' => '2024-03-29', 'ref' => 'WIRE-2024-0329', 'status' => 'paid'],
            ['contract' => 3, 'desc' => '50% al Ritiro',       'amount' => 16000,   'pct' => 50, 'due' => now()->addDays(3)->format('Y-m-d'), 'paid' => null, 'ref' => null, 'status' => 'pending'],
            ['contract' => 4, 'desc' => '30% Deposito',        'amount' => 20400,   'pct' => 30, 'due' => '2024-04-10', 'paid' => '2024-04-09', 'ref' => 'WIRE-2024-0409', 'status' => 'paid'],
            ['contract' => 4, 'desc' => '70% BL',              'amount' => 47600,   'pct' => 70, 'due' => now()->addDays(6)->format('Y-m-d'), 'paid' => null, 'ref' => null, 'status' => 'pending'],
        ];

        foreach ($paymentsData as $pd) {
            Payment::create([
                'contract_id'           => $contracts[$pd['contract']]->id,
                'description'           => $pd['desc'],
                'amount'                => $pd['amount'],
                'percentage'            => $pd['pct'],
                'due_date'              => $pd['due'],
                'paid_date'             => $pd['paid'],
                'transaction_reference' => $pd['ref'],
                'status'                => $pd['status'],
            ]);
        }
    }
}
