<?php namespace App\Transformers;

use App\Group;
use League\Fractal\TransformerAbstract;

class GroupTransformer extends TransformerAbstract {

    public function transform(Group $group) {

        return [
            'id' => (int)$group->id,
            'group_name' => $group->group_name,
            'description' => $group->description,
            'image_url' => $group->group_image_url
        ];
    }
}