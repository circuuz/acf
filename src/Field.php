<?php

/*
 * This file is part of WordPlate.
 *
 * (c) Vincent Klaiber <hello@vinkla.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace WordPlate\Acf;

use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * This is the field class.
 *
 * @author Vincent Klaiber <hello@vinkla.com>
 */
class Field
{
    /**
     * The field key.
     *
     * @var string
     */
    protected $key;

    /**
     * The group instance.
     *
     * @var \WordPlate\Acf\Group
     */
    protected $group;

    /**
     * The settings array.
     *
     * @var array
     */
    protected $settings;

    /**
     * The group keys.
     *
     * @var array
     */
    protected static $keys = [];

    /**
     * Create a new field instance.
     *
     * @param \WordPlate\Acf\Group $group
     * @param array $settings
     *
     * @return void
     */
    public function __construct(Group $group, array $settings, Field $parentField = null)
    {
        $this->group = $group;
        $this->settings = $settings;
        $this->parentField = $parentField;

        $this->setKey();
    }

    /**
     * Set the field key.
     *
     * @param string $key
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function setKey()
    {
        if ($this->parentField) {
            $prefix = str_replace('field_', '', $this->parentField->getKey());
        } else {
            $prefix = str_replace('group_', '', $this->group->getKey());
        }

        $name = ('tab' === $this->settings['type']) ? uniqid() : Str::snake($this->settings['name']);

        $key = sprintf('field_%s_%s', $prefix, $name);

        if (in_array($key, self::$keys)) {
            throw new InvalidArgumentException("The field key [$key] is not unique.");
        }

        self::$keys[] = $key;

        $this->key = $key;
    }

    /**
     * Get the field key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get the conditional logic.
     *
     * @return array
     */
    public function getConditionalLogic(): array
    {
        $conditionalLogic = [];

        $prefix = str_replace('group_', '', $this->group->getKey());

        foreach ($this->settings['conditional_logic'] as $rules) {
            $group = [];

            foreach ($rules as $rule) {
                $name = Str::snake($rule['name']);

                $field = sprintf('field_%s_%s', $prefix, $name);

                $rule = [
                    'field' => $field,
                    'operator' => $rule['operator'],
                    'value' => $rule['value'],
                ];

                $group[] = $rule;
            }

            $conditionalLogic[] = $group;
        }

        return $conditionalLogic;
    }

    /**
     * Get the sub fields.
     *
     * @return array
     */
    public function getSubFields()
    {
        $fields = [];

        foreach ($this->settings['sub_fields'] as $field) {
            $field = new self($this->group, $field, $this);

            $fields[] = $field->toArray();
        }

        return $fields;
    }

    /**
     * Get the sub fields.
     *
     * @return array
     */
    public function getLayouts()
    {
        $fields = [];

        foreach ($this->settings['layouts'] as $field) {
            $field = new self($this->group, $field, $this);

            $fields[] = $field->toArray();
        }

        return $fields;
    }

    /**
     * Return the field as array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $settings = [
            'key' => $this->getKey(),
        ];

        if (isset($this->settings['conditional_logic'])) {
            $settings['conditional_logic'] = $this->getConditionalLogic();
        }

        if (isset($this->settings['sub_fields']) && is_array($this->settings['sub_fields'])) {
            $settings['sub_fields'] = $this->getSubFields();
        }

        if (isset($this->settings['layouts']) && is_array($this->settings['layouts'])) {
            $settings['layouts'] = $this->getLayouts();
        }

        return array_merge($this->settings, $settings);
    }
}
