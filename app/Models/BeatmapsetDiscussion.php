<?php

/**
 *    Copyright 2015 ppy Pty. Ltd.
 *
 *    This file is part of osu!web. osu!web is distributed with the hope of
 *    attracting more community contributions to the core ecosystem of osu!.
 *
 *    osu!web is free software: you can redistribute it and/or modify
 *    it under the terms of the Affero GNU General Public License version 3
 *    as published by the Free Software Foundation.
 *
 *    osu!web is distributed WITHOUT ANY WARRANTY; without even the implied
 *    warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *    See the GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with osu!web.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace App\Models;

use App\Transformers\BeatmapsetDiscussionTransformer;
use Illuminate\Database\Eloquent\Model;

class BeatmapsetDiscussion extends Model
{
    protected $guarded = [];

    public function beatmapset()
    {
        return $this->belongsTo(Beatmapset::class);
    }

    public function beatmapDiscussions()
    {
        return $this->hasMany(BeatmapDiscussion::class);
    }

    public function user()
    {
        return $this->beatmapset->user();
    }

    public function defaultJson($currentUser = null)
    {
        $includes = [
            'beatmap_discussions.beatmap_discussion_posts',
            'users',
        ];

        if ($currentUser !== null) {
            $includes[] = "beatmap_discussions.current_user_attributes:user_id({$currentUser->user_id})";
        }

        var_dump('before calling static::with(2): '.xxzx());
        static::with(['beatmapDiscussions', 'beatmapDiscussions.beatmapDiscussionPosts'])->find($this->id);
        var_dump('after calling static::with(2): '.xxzx());

        var_dump('before calling static::with(2b): '.xxzx());
        static::with(['beatmapDiscussions', 'beatmapDiscussions.beatmapDiscussionVotes'])->find($this->id);
        var_dump('after calling static::with(2b): '.xxzx());

        var_dump('before calling static::with(3): '.xxzx());
        static::with(['beatmapDiscussions', 'beatmapDiscussions.beatmapDiscussionPosts', 'beatmapDiscussions.beatmapDiscussionVotes'])->find($this->id);
        var_dump('after calling static::with(3): '.xxzx());

        return fractal_item_array(
            static::with([
                // breakage in travis without this include
                'beatmapDiscussions',
                'beatmapDiscussions.beatmapDiscussionPosts',
                'beatmapDiscussions.beatmapDiscussionVotes',
            ])->find($this->id),
            new BeatmapsetDiscussionTransformer(),
            implode(',', $includes)
        );
    }
}
