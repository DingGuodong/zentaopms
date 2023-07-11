<?php
declare(strict_types=1);
namespace zin;

$getAffectedTabs = function($story, $users)
{
    global $lang, $config;
    $affectedProjects  = array();
    $affectedTaskCount = 0;
    foreach($story->executions as $executionID => $execution)
    {
        $teams = '';
        foreach($story->teams[$executionID] as $member) $teams .= zget($users, $member) . ' ';
        $affectedTaskCount += count(zget($story->tasks, $executionID, array()));
        $affectedProjects[] = h6
        (
            $execution->name,
            $teams ? small(icon('group'), $teams) : null
        );
        $affectedProjects[] = dtable
        (
            set::cols($config->story->affect->projects->fields),
            set::data(array_values(zget($story->tasks, $executionID, array())))
        );
    }

    return formGroup
    (
        setClass('w-full'),
        set::label($lang->story->checkAffection),
        set::required(false),
        tabs
        (
            setClass('w-full'),
            tabPane
            (
                to::suffix(label($affectedTaskCount)),
                set::key('affectedProjects'),
                set::title($lang->story->affectedProjects),
                set::active(true),
                $affectedProjects,
            ),
            tabPane
            (
                to::suffix(label(count($story->bugs))),
                set::key('affectedBugs'),
                set::title($lang->story->affectedBugs),
                empty($story->bugs) ? null : dtable
                (
                    set::cols($config->story->affect->bugs->fields),
                    set::data(array_values($story->bugs))
                )
            ),
            tabPane
            (
                to::suffix(label(count($story->cases))),
                set::key('affectedCases'),
                set::title($lang->story->affectedCases),
                empty($story->cases) ? null : dtable
                (
                    set::cols($config->story->affect->cases->fields),
                    set::data(array_values($story->cases))
                )
            ),
            empty($story->twins) ? null : tabPane
            (
                to::suffix(label(count($story->twins))),
                set::key('affectedTwins'),
                set::title($lang->story->affectedTwins),
                dtable
                (
                    set::cols($config->story->affect->twins->fields),
                    set::data(array_values($story->twins))
                )
            ),
        )
    );
}
