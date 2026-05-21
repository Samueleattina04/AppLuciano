<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Contract;
use App\Models\Shipment;
use App\Models\Payment;
use App\Models\Claim;
use App\Models\CommunicationTask;
use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $userId = $user?->id;
        $now = Carbon::now();

        // ===== SUPPLIERS =====
        $suppliers = [];
        $supplierData = [
            ['name' => 'Shenzhen FastMake Co. Ltd', 'country' => 'China', 'contact_name' => 'Wang Wei', 'contact_email' => 'wangwei@fastmake.cn', 'contact_phone' => '+86 755 1234 5678'],
            ['name' => 'Vietnam Green Foods JSC', 'country' => 'Vietnam', 'contact_name' => 'Nguyen Van Hai', 'contact_email' => 'nvhai@greenfoods.vn', 'contact_phone' => '+84 28 3456 7890'],
            ['name' => 'Bangladesh Agro Industries Ltd', 'country' => 'Bangladesh', 'contact_name' => 'Rahman Karim', 'contact_email' => 'rkarim@bai.com.bd', 'contact_phone' => '+880 2 9876 543'],
            ['name' => 'Ivory Coast Commodities SA', 'country' => 'Ivory Coast', 'contact_name' => 'Kouassi Thierry', 'contact_email' => 'kthierry@ivcoast-commodities.ci', 'contact_phone' => '+225 20 21 3456'],
        ];
        foreach ($supplierData as $data) {
            $suppliers[] = Supplier::create($data);
        }

        // ===== PRODUCTS =====
        $products = [];
        $productData = [
            ['name' => 'Frozen Shrimp', 'category' => 'Seafood', 'unit_of_measure' => 'kg', 'description' => 'IQF Vannamei shrimp'],
            ['name' => 'Jasmine Rice', 'category' => 'Cereals', 'unit_of_measure' => 'MT', 'description' => 'Thai Jasmine long grain rice'],
            ['name' => 'Organic Cashew', 'category' => 'Nuts', 'unit_of_measure' => 'kg', 'description' => 'Organic certified cashew kernels W320'],
            ['name' => 'Green Coffee', 'category' => 'Coffee', 'unit_of_measure' => 'kg', 'description' => 'Robusta green coffee beans'],
            ['name' => 'Palm Oil', 'category' => 'Oils', 'unit_of_measure' => 'MT', 'description' => 'RBD Palm Oil'],
        ];
        foreach ($productData as $data) {
            $products[] = Product::create($data);
        }

        // ===== CONTRACTS =====
        // ANT-2025-001: Frozen Shrimp, confirmed, partially shipped
        $c1 = Contract::create([
            'contract_number'   => 'ANT-2025-001',
            'supplier_id'       => $suppliers[0]->id,
            'product_id'        => $products[0]->id,
            'type'              => 'single',
            'crop_season'       => '2025',
            'quantity_contracted' => 50000,
            'unit_of_measure'   => 'kg',
            'unit_price'        => 1.70,
            'currency'          => 'USD',
            'incoterm'          => 'FOB',
            'port_of_loading'   => 'Shanghai',
            'port_of_discharge' => 'Genova',
            'contract_date'     => $now->copy()->subMonths(3)->toDateString(),
            'shipment_window_start' => $now->copy()->subMonths(2)->toDateString(),
            'shipment_window_end'   => $now->copy()->addMonth()->toDateString(),
            'payment_terms_description' => '30% advance + 70% against BL copy',
            'total_value'       => 85000.00,
            'status'            => 'partially_shipped',
            'created_by'        => $userId,
        ]);

        // ANT-2025-002: Jasmine Rice, confirmed
        $c2 = Contract::create([
            'contract_number'   => 'ANT-2025-002',
            'supplier_id'       => $suppliers[1]->id,
            'product_id'        => $products[1]->id,
            'type'              => 'single',
            'quantity_contracted' => 200,
            'unit_of_measure'   => 'MT',
            'unit_price'        => 480.00,
            'currency'          => 'USD',
            'incoterm'          => 'CIF',
            'port_of_loading'   => 'Ho Chi Minh City',
            'port_of_discharge' => 'Genova',
            'contract_date'     => $now->copy()->subMonths(2)->toDateString(),
            'payment_terms_description' => '100% LC at sight',
            'total_value'       => 96000.00,
            'status'            => 'confirmed',
            'created_by'        => $userId,
        ]);

        // ANT-2025-003: Organic Cashew, draft
        $c3 = Contract::create([
            'contract_number'   => 'ANT-2025-003',
            'supplier_id'       => $suppliers[2]->id,
            'product_id'        => $products[2]->id,
            'type'              => 'single',
            'quantity_contracted' => 20000,
            'unit_of_measure'   => 'kg',
            'unit_price'        => 2.70,
            'currency'          => 'USD',
            'incoterm'          => 'FOB',
            'port_of_loading'   => 'Chittagong',
            'port_of_discharge' => 'La Spezia',
            'contract_date'     => $now->copy()->subWeeks(2)->toDateString(),
            'payment_terms_description' => '50% advance, 50% BL',
            'total_value'       => 54000.00,
            'status'            => 'draft',
            'created_by'        => $userId,
        ]);

        // ANT-2025-004: Green Coffee, confirmed, partially shipped
        $c4 = Contract::create([
            'contract_number'   => 'ANT-2025-004',
            'supplier_id'       => $suppliers[3]->id,
            'product_id'        => $products[3]->id,
            'type'              => 'single',
            'quantity_contracted' => 15000,
            'unit_of_measure'   => 'kg',
            'unit_price'        => 4.15,
            'currency'          => 'USD',
            'incoterm'          => 'CIF',
            'port_of_loading'   => 'Abidjan',
            'port_of_discharge' => 'La Spezia',
            'contract_date'     => $now->copy()->subMonths(4)->toDateString(),
            'payment_terms_description' => '30% advance + 70% sight draft',
            'total_value'       => 62250.00,
            'status'            => 'partially_shipped',
            'created_by'        => $userId,
        ]);

        // ===== SHIPMENTS =====
        $s1 = Shipment::create([
            'shipment_code'     => 'SHP-2025-001',
            'contract_id'       => $c1->id,
            'supplier_id'       => $suppliers[0]->id,
            'product_id'        => $products[0]->id,
            'container_number'  => 'TCKU3456789',
            'seal_number'       => 'SL001234',
            'quantity_shipped'  => 25000,
            'vessel_name'       => 'MSC Beatrice',
            'voyage_number'     => '0521N',
            'carrier'           => 'MSC',
            'forwarder'         => 'Kuehne+Nagel',
            'bl_number'         => 'MSCUGN123456',
            'port_of_loading'   => 'Shanghai',
            'port_of_discharge' => 'Genova',
            'etd'               => $now->copy()->subDays(25)->toDateString(),
            'eta'               => $now->copy()->addDays(5)->toDateString(),
            'status'            => 'in_transit',
            'created_by'        => $userId,
        ]);

        $s2 = Shipment::create([
            'shipment_code'     => 'SHP-2025-002',
            'contract_id'       => $c4->id,
            'supplier_id'       => $suppliers[3]->id,
            'product_id'        => $products[3]->id,
            'container_number'  => 'MSCU7812340',
            'quantity_shipped'  => 7500,
            'vessel_name'       => 'MSC Beatrice',
            'voyage_number'     => '0510E',
            'carrier'           => 'MSC',
            'bl_number'         => 'MSCUAB654321',
            'port_of_loading'   => 'Abidjan',
            'port_of_discharge' => 'La Spezia',
            'etd'               => $now->copy()->subDays(45)->toDateString(),
            'eta'               => $now->copy()->subDays(10)->toDateString(),
            'actual_arrival_date' => $now->copy()->subDays(10)->toDateString(),
            'status'            => 'arrived_pod',
            'created_by'        => $userId,
        ]);

        $s3 = Shipment::create([
            'shipment_code'     => 'SHP-2025-003',
            'contract_id'       => $c1->id,
            'supplier_id'       => $suppliers[0]->id,
            'product_id'        => $products[0]->id,
            'container_number'  => 'OOLU5543210',
            'quantity_shipped'  => 12500,
            'vessel_name'       => 'COSCO Excellence',
            'voyage_number'     => 'CE0498',
            'carrier'           => 'COSCO',
            'bl_number'         => 'COSU987654321',
            'port_of_loading'   => 'Shanghai',
            'port_of_discharge' => 'Genova',
            'etd'               => $now->copy()->subDays(38)->toDateString(),
            'eta'               => $now->copy()->subDays(3)->toDateString(),
            'actual_arrival_date' => $now->copy()->subDays(3)->toDateString(),
            'status'            => 'customs_clearance',
            'created_by'        => $userId,
        ]);

        $s4 = Shipment::create([
            'shipment_code'     => 'SHP-2025-004',
            'contract_id'       => $c2->id,
            'supplier_id'       => $suppliers[1]->id,
            'product_id'        => $products[1]->id,
            'container_number'  => 'APMU1234567',
            'quantity_shipped'  => 100,
            'port_of_loading'   => 'Ho Chi Minh City',
            'port_of_discharge' => 'Genova',
            'etd'               => $now->copy()->addDays(15)->toDateString(),
            'eta'               => $now->copy()->addDays(45)->toDateString(),
            'status'            => 'in_production',
            'created_by'        => $userId,
        ]);

        $s5 = Shipment::create([
            'shipment_code'     => 'SHP-2025-005',
            'contract_id'       => $c4->id,
            'supplier_id'       => $suppliers[3]->id,
            'product_id'        => $products[3]->id,
            'container_number'  => 'HLXU4321098',
            'quantity_shipped'  => 7500,
            'vessel_name'       => 'Hapag Leipzig',
            'voyage_number'     => 'HL0234',
            'carrier'           => 'Hapag-Lloyd',
            'port_of_loading'   => 'Abidjan',
            'port_of_discharge' => 'La Spezia',
            'etd'               => $now->copy()->addDays(3)->toDateString(),
            'eta'               => $now->copy()->addDays(28)->toDateString(),
            'status'            => 'at_port',
            'created_by'        => $userId,
        ]);

        $s6 = Shipment::create([
            'shipment_code'     => 'SHP-2025-006',
            'contract_id'       => $c2->id,
            'supplier_id'       => $suppliers[1]->id,
            'product_id'        => $products[1]->id,
            'container_number'  => 'BICU9876543',
            'quantity_shipped'  => 100,
            'port_of_loading'   => 'Ho Chi Minh City',
            'port_of_discharge' => 'Genova',
            'etd'               => $now->copy()->subDays(60)->toDateString(),
            'eta'               => $now->copy()->subDays(25)->toDateString(),
            'actual_arrival_date' => $now->copy()->subDays(25)->toDateString(),
            'warehouse_arrival_date' => $now->copy()->subDays(20)->toDateString(),
            'status'            => 'delivered_warehouse',
            'created_by'        => $userId,
        ]);

        // ===== PAYMENTS =====
        // Overdue payments
        Payment::create(['contract_id' => $c1->id, 'supplier_id' => $suppliers[0]->id, 'amount_due' => 25500.00, 'amount_paid' => 0, 'currency' => 'USD', 'due_date' => $now->copy()->subDays(15)->toDateString(), 'status' => 'overdue', 'notes' => '70% balance ANT-2025-001', 'created_by' => $userId]);
        Payment::create(['contract_id' => $c4->id, 'supplier_id' => $suppliers[3]->id, 'shipment_id' => $s2->id, 'amount_due' => 18675.00, 'amount_paid' => 0, 'currency' => 'USD', 'due_date' => $now->copy()->subDays(8)->toDateString(), 'status' => 'overdue', 'notes' => '70% balance SHP-2025-002', 'created_by' => $userId]);

        // Due soon payments
        Payment::create(['contract_id' => $c1->id, 'supplier_id' => $suppliers[0]->id, 'shipment_id' => $s1->id, 'amount_due' => 17500.00, 'amount_paid' => 0, 'currency' => 'USD', 'due_date' => $now->copy()->addDays(3)->toDateString(), 'status' => 'due_soon', 'notes' => '70% SHP-2025-001 against BL', 'created_by' => $userId]);
        Payment::create(['contract_id' => $c2->id, 'supplier_id' => $suppliers[1]->id, 'amount_due' => 48000.00, 'amount_paid' => 0, 'currency' => 'USD', 'due_date' => $now->copy()->addDays(5)->toDateString(), 'status' => 'due_soon', 'notes' => '50% advance ANT-2025-002', 'created_by' => $userId]);
        Payment::create(['contract_id' => $c4->id, 'supplier_id' => $suppliers[3]->id, 'amount_due' => 9337.50, 'amount_paid' => 0, 'currency' => 'USD', 'due_date' => $now->copy()->addDays(7)->toDateString(), 'status' => 'due_soon', 'notes' => '30% advance ANT-2025-004 SHP2', 'created_by' => $userId]);

        // Paid payment
        Payment::create(['contract_id' => $c1->id, 'supplier_id' => $suppliers[0]->id, 'amount_due' => 25500.00, 'amount_paid' => 25500.00, 'currency' => 'USD', 'due_date' => $now->copy()->subMonths(3)->toDateString(), 'payment_date' => $now->copy()->subMonths(3)->toDateString(), 'bank_reference' => 'TRF-2025-0123', 'status' => 'paid', 'notes' => '30% advance ANT-2025-001', 'created_by' => $userId]);
        Payment::create(['contract_id' => $c4->id, 'supplier_id' => $suppliers[3]->id, 'amount_due' => 18675.00, 'amount_paid' => 18675.00, 'currency' => 'USD', 'due_date' => $now->copy()->subMonths(4)->toDateString(), 'payment_date' => $now->copy()->subMonths(4)->subDays(2)->toDateString(), 'bank_reference' => 'TRF-2025-0088', 'status' => 'paid', 'notes' => '30% advance ANT-2025-004', 'created_by' => $userId]);

        // Pending
        Payment::create(['contract_id' => $c3->id, 'supplier_id' => $suppliers[2]->id, 'amount_due' => 27000.00, 'amount_paid' => 0, 'currency' => 'USD', 'due_date' => $now->copy()->addDays(30)->toDateString(), 'status' => 'pending', 'notes' => '50% advance ANT-2025-003', 'created_by' => $userId]);

        // ===== CLAIMS =====
        Claim::create([
            'contract_id' => $c1->id,
            'shipment_id' => $s2->id,
            'supplier_id' => $suppliers[0]->id,
            'claim_type'  => 'quality',
            'amount'      => 3200.00,
            'currency'    => 'USD',
            'reason'      => 'Quality analysis showed 15% of shrimp grade 2 instead of contracted grade 1. Moisture content above specification.',
            'status'      => 'open',
            'notes'       => 'Lab report attached. Waiting supplier response.',
            'created_by'  => $userId,
        ]);

        Claim::create([
            'contract_id' => $c4->id,
            'shipment_id' => $s2->id,
            'supplier_id' => $suppliers[3]->id,
            'claim_type'  => 'weight_shortage',
            'amount'      => 850.00,
            'currency'    => 'USD',
            'reason'      => 'Net weight verified at warehouse: 7,296 kg instead of contracted 7,500 kg. Shortage of 204 kg.',
            'status'      => 'under_review',
            'notes'       => 'Weight certificate and warehouse receipt attached.',
            'created_by'  => $userId,
        ]);

        Claim::create([
            'contract_id' => $c2->id,
            'shipment_id' => $s6->id,
            'supplier_id' => $suppliers[1]->id,
            'claim_type'  => 'packaging',
            'amount'      => 1200.00,
            'currency'    => 'USD',
            'reason'      => 'Several bags damaged during transit, packaging non-compliant with specification.',
            'status'      => 'closed',
            'resolved_date' => $now->copy()->subDays(5)->toDateString(),
            'notes'       => 'Resolved: supplier issued credit note.',
            'created_by'  => $userId,
        ]);

        // ===== COMMUNICATION TASKS =====
        CommunicationTask::create([
            'subject'     => 'URGENT: BL Draft Approval Required - TCKU3456789',
            'sender'      => 'forwarder@kuehne-nagel.com',
            'recipient'   => 'supply@company.it',
            'category'    => 'document_approval',
            'priority'    => 'urgent',
            'due_date'    => $now->copy()->addDays(1)->toDateString(),
            'status'      => 'to_review',
            'notes'       => 'BL draft received from K+N. Need to approve before vessel departure.',
            'contract_id' => $c1->id,
            'shipment_id' => $s1->id,
            'assigned_to' => $userId,
            'created_by'  => $userId,
        ]);

        CommunicationTask::create([
            'subject'     => 'URGENT: CAD Documents Pending - ANT-2025-004',
            'sender'      => 'kthierry@ivcoast-commodities.ci',
            'recipient'   => 'supply@company.it',
            'category'    => 'cad_bank_request',
            'priority'    => 'urgent',
            'due_date'    => $now->copy()->toDateString(),
            'status'      => 'waiting_internal',
            'notes'       => 'Bank has requested original documents. CAD must be processed before release.',
            'contract_id' => $c4->id,
            'assigned_to' => $userId,
            'created_by'  => $userId,
        ]);

        CommunicationTask::create([
            'subject'     => 'Payment Follow-up: TRF overdue ANT-2025-001',
            'recipient'   => 'wangwei@fastmake.cn',
            'category'    => 'payment_followup',
            'priority'    => 'high',
            'due_date'    => $now->copy()->addDays(2)->toDateString(),
            'status'      => 'ready_to_reply',
            'notes'       => 'Need to confirm payment date with finance and send remittance.',
            'contract_id' => $c1->id,
            'assigned_to' => $userId,
            'created_by'  => $userId,
        ]);

        CommunicationTask::create([
            'subject'     => 'Phytosanitary certificate request - SHP-2025-005',
            'sender'      => 'supply@company.it',
            'recipient'   => 'kthierry@ivcoast-commodities.ci',
            'category'    => 'supplier_request',
            'priority'    => 'normal',
            'due_date'    => $now->copy()->addDays(7)->toDateString(),
            'status'      => 'waiting_supplier',
            'notes'       => 'Requested original phytosanitary certificate for customs.',
            'contract_id' => $c4->id,
            'shipment_id' => $s5->id,
            'created_by'  => $userId,
        ]);

        CommunicationTask::create([
            'subject'     => 'Customs clearance update - SHP-2025-003',
            'category'    => 'internal_note',
            'priority'    => 'normal',
            'status'      => 'replied',
            'notes'       => 'Customs agent confirmed clearance expected within 48h. All documents OK.',
            'shipment_id' => $s3->id,
            'created_by'  => $userId,
        ]);

        // ===== ACTIVITY LOGS =====
        $models = [
            ['log_name' => 'Contract', 'description' => 'created', 'subject_type' => Contract::class, 'subject_id' => $c1->id],
            ['log_name' => 'Contract', 'description' => 'created', 'subject_type' => Contract::class, 'subject_id' => $c2->id],
            ['log_name' => 'Contract', 'description' => 'created', 'subject_type' => Contract::class, 'subject_id' => $c4->id],
            ['log_name' => 'Contract', 'description' => 'updated', 'subject_type' => Contract::class, 'subject_id' => $c1->id, 'properties' => json_encode(['status' => 'partially_shipped'])],
            ['log_name' => 'Shipment', 'description' => 'created', 'subject_type' => Shipment::class, 'subject_id' => $s1->id],
            ['log_name' => 'Shipment', 'description' => 'created', 'subject_type' => Shipment::class, 'subject_id' => $s2->id],
            ['log_name' => 'Shipment', 'description' => 'updated', 'subject_type' => Shipment::class, 'subject_id' => $s2->id, 'properties' => json_encode(['status' => 'arrived_pod', 'actual_arrival_date' => $now->copy()->subDays(10)->toDateString()])],
            ['log_name' => 'Shipment', 'description' => 'updated', 'subject_type' => Shipment::class, 'subject_id' => $s3->id, 'properties' => json_encode(['status' => 'customs_clearance'])],
            ['log_name' => 'Payment', 'description' => 'created', 'subject_type' => Payment::class, 'subject_id' => 1],
            ['log_name' => 'Payment', 'description' => 'updated', 'subject_type' => Payment::class, 'subject_id' => 6, 'properties' => json_encode(['status' => 'paid', 'payment_date' => $now->copy()->subMonths(3)->toDateString()])],
            ['log_name' => 'Claim', 'description' => 'created', 'subject_type' => Claim::class, 'subject_id' => 1],
            ['log_name' => 'Claim', 'description' => 'updated', 'subject_type' => Claim::class, 'subject_id' => 2, 'properties' => json_encode(['status' => 'under_review'])],
        ];

        foreach ($models as $i => $data) {
            ActivityLog::create(array_merge($data, [
                'causer_type' => $userId ? get_class($user) : null,
                'causer_id'   => $userId,
                'created_at'  => $now->copy()->subHours($i * 3),
                'updated_at'  => $now->copy()->subHours($i * 3),
            ]));
        }
    }
}
