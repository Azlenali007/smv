<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'site_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'key',
        'value',
        'type',
        'description',
        'updated_at',
    ];

    protected $useTimestamps = false;

    public function getSetting(string $key, $default = null)
    {
        $setting = $this->where('key', $key)->first();
        if (!$setting) {
            return $default;
        }
        return $setting['value'];
    }

    public function setSetting(string $key, $value, string $type = 'string', ?string $description = null): bool
    {
        $existing = $this->where('key', $key)->first();
        $data = [
            'value'      => is_array($value) ? json_encode($value) : (string)$value,
            'type'       => $type,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($description) {
            $data['description'] = $description;
        }

        if ($existing) {
            return $this->update($existing['id'], $data);
        }

        $data['key'] = $key;
        return (bool)$this->insert($data);
    }

    public function getAllAsAssociative(): array
    {
        $settings = $this->findAll();
        $result = [];
        foreach ($settings as $s) {
            $result[$s['key']] = $s['value'];
        }
        return $result;
    }
}
