<?php

namespace App\Http\Resources;

/**
 * @property array $resource
 * @see LetterTemplateService::GROUPED_LETTER_TEMPLATES
 */
class GroupedLetterTemplatesResource extends BaseResource
{
    public function toArray($request): array
    {
        $result = [];

        foreach ($this->resource as $groupName => $letterNames) {
            $group = [
                'group_label' => __("letter_templates.groups.{$groupName}"),
                'letter_templates' => [],
            ];

            foreach ($letterNames as $letterName) {
                $group['letter_templates'][] = [
                    'name' => $letterName,
                    'label' => __("letter_templates.letter_names.{$letterName}"),
                ];
            }

            $result[] = $group;
        }

        return $result;
    }
}
