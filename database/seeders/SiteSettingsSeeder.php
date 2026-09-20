<?php

namespace Database\Seeders;

use App\Models\ContactChannel;
use App\Models\EmailRoutingRule;
use App\Models\SiteSetting;
use App\Services\SiteSettingsService;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'company_name',    'value' => 'muzarwa',                                   'group' => 'general', 'type' => 'text'],
            ['key' => 'company_tagline', 'value' => 'Taste the Passion. Fuel Your Flavour.',     'group' => 'general', 'type' => 'text'],
            ['key' => 'hours_weekday',   'value' => 'Mon–Fri · 8:30–17:30',                      'group' => 'contact', 'type' => 'text'],
            ['key' => 'hours_saturday',  'value' => 'Sat · 9:00–13:00 · CAT',                    'group' => 'contact', 'type' => 'text'],
            ['key' => 'hours_sunday',    'value' => 'Sun · Closed',                               'group' => 'contact', 'type' => 'text'],
            [
                'key'   => 'maps_embed_url',
                'value' => 'https://maps.google.com/maps?q=Rwamagana%2C%20Rwanda&hl=en&z=11&output=embed',
                'group' => 'contact',
                'type'  => 'text',
            ],
            [
                'key'   => 'maps_open_url',
                'value' => 'https://www.google.com/maps/search/?api=1&query=Rwamagana%2C+Rwanda',
                'group' => 'contact',
                'type'  => 'text',
            ],
        ];

        foreach ($settings as $row) {
            SiteSetting::updateOrCreate(['key' => $row['key']], $row);
        }

        $channels = [
            ['type' => 'phone',    'label' => 'Phone',             'value' => '+250 784 421 127',  'is_primary' => true,  'is_active' => true, 'sort_order' => 1],
            ['type' => 'email',    'label' => 'Email',             'value' => 'info@muzarwa.com',  'is_primary' => true,  'is_active' => true, 'sort_order' => 1],
            ['type' => 'whatsapp', 'label' => 'WhatsApp',          'value' => '250784421127',      'is_primary' => true,  'is_active' => true, 'sort_order' => 1],
            ['type' => 'address',  'label' => 'Location',          'value' => 'Rwamagana, Rwanda', 'is_primary' => true,  'is_active' => true, 'sort_order' => 1],
        ];

        $keepIds = [];

        foreach ($channels as $channel) {
            $record = ContactChannel::updateOrCreate(
                ['type' => $channel['type'], 'is_primary' => true],
                $channel
            );
            $keepIds[] = $record->id;
        }

        ContactChannel::query()
            ->whereIn('type', ['phone', 'email', 'whatsapp', 'address'])
            ->whereNotIn('id', $keepIds)
            ->update(['is_active' => false, 'is_primary' => false]);

        $rules = [
            ['event' => 'contact_form', 'recipient_email' => 'info@muzarwa.com', 'recipient_name' => 'muzarwa', 'is_active' => true],
            ['event' => 'order_placed', 'recipient_email' => 'info@muzarwa.com', 'recipient_name' => 'Orders',  'is_active' => true],
        ];

        foreach ($rules as $rule) {
            EmailRoutingRule::updateOrCreate(
                ['event' => $rule['event'], 'recipient_email' => $rule['recipient_email']],
                $rule
            );
        }

        EmailRoutingRule::query()
            ->where('recipient_email', 'like', '%shakyltd%')
            ->orWhere('recipient_email', 'like', '%gaio.rw%')
            ->update(['is_active' => false]);

        app(SiteSettingsService::class)->flush();
    }
}
