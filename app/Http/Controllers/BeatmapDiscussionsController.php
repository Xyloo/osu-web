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
namespace App\Http\Controllers;

use Auth;
use App\Models\BeatmapDiscussion;
use App\Models\BeatmapsetDiscussion;
use Request;

class BeatmapDiscussionsController extends Controller
{
    protected $section = 'beatmaps';

    public function __construct()
    {
        $this->middleware('auth');

        return parent::__construct();
    }

    public function vote($id)
    {
        var_dump('enter vote: '.xxzx());
        $discussion = BeatmapDiscussion::findOrFail($id);

        priv_check('BeatmapDiscussionVote', $discussion)->ensureCan();

        $params = get_params(Request::all(), 'beatmap_discussion_vote',
            ['score:int'],
            [],
            [
                'user_id' => Auth::user()->user_id,
            ]
        );

        var_dump($params);

        if ($discussion->vote($params)) {
            // var_dump('after save: '.xxzx());
            // var_dump(\App\Models\Beatmapset::first()->toArray());

            try {
                // var_dump($discussion->beatmapsetDiscussion->beatmapset_id);
                // var_dump(\App\Models\Beatmapset::find($discussion->beatmapset_id));
                $discussion->beatmapsetDiscussion->defaultJson(Auth::user());
            } catch (\Exception $e) {
                var_dump('after exception: '.xxzx());
                var_dump($e);
            }

            return $discussion->beatmapsetDiscussion->defaultJson(Auth::user());
        } else {
            // var_dump('after failing save: '.xxzx());

            return error_popup(trans('beatmaps.discussion-votes.update.error'));
        }
    }
}
