<?php


namespace Kamille\Utils\StepTracker;


interface StepTrackerAwareInterface
{

    public function setStepTracker(StepTrackerInterface $stepTracker);
}

