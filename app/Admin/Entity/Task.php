<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Task
 */
class Task
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $pid;

    /**
     * @var integer
     */
    private $issueId;

    /**
     * @var integer
     */
    private $types;

    /**
     * @var string
     */
    private $names;

    /**
     * @var integer
     */
    private $priority;

    /**
     * @var integer
     */
    private $executor;

    /**
     * @var integer
     */
    private $nums;

    /**
     * @var integer
     */
    private $cycleTypes;

    /**
     * @var integer
     */
    private $cycleTime;

    /**
     * @var \DateTime
     */
    private $deadline;

    /**
     * @var float
     */
    private $acorn;

    /**
     * @var integer
     */
    private $medal;

    /**
     * @var string
     */
    private $learns;

    /**
     * @var string
     */
    private $content;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var \DateTime
     */
    private $endTime;

    /**
     * @var integer
     */
    private $status;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pid
     *
     * @param integer $pid
     * @return Task
     */
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * Get pid
     *
     * @return integer 
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set issueId
     *
     * @param integer $issueId
     * @return Task
     */
    public function setIssueId($issueId)
    {
        $this->issueId = $issueId;

        return $this;
    }

    /**
     * Get issueId
     *
     * @return integer 
     */
    public function getIssueId()
    {
        return $this->issueId;
    }

    /**
     * Set types
     *
     * @param integer $types
     * @return Task
     */
    public function setTypes($types)
    {
        $this->types = $types;

        return $this;
    }

    /**
     * Get types
     *
     * @return integer 
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Set names
     *
     * @param string $names
     * @return Task
     */
    public function setNames($names)
    {
        $this->names = $names;

        return $this;
    }

    /**
     * Get names
     *
     * @return string 
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return Task
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer 
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set executor
     *
     * @param integer $executor
     * @return Task
     */
    public function setExecutor($executor)
    {
        $this->executor = $executor;

        return $this;
    }

    /**
     * Get executor
     *
     * @return integer 
     */
    public function getExecutor()
    {
        return $this->executor;
    }

    /**
     * Set nums
     *
     * @param integer $nums
     * @return Task
     */
    public function setNums($nums)
    {
        $this->nums = $nums;

        return $this;
    }

    /**
     * Get nums
     *
     * @return integer 
     */
    public function getNums()
    {
        return $this->nums;
    }

    /**
     * Set cycleTypes
     *
     * @param integer $cycleTypes
     * @return Task
     */
    public function setCycleTypes($cycleTypes)
    {
        $this->cycleTypes = $cycleTypes;

        return $this;
    }

    /**
     * Get cycleTypes
     *
     * @return integer 
     */
    public function getCycleTypes()
    {
        return $this->cycleTypes;
    }

    /**
     * Set cycleTime
     *
     * @param integer $cycleTime
     * @return Task
     */
    public function setCycleTime($cycleTime)
    {
        $this->cycleTime = $cycleTime;

        return $this;
    }

    /**
     * Get cycleTime
     *
     * @return integer 
     */
    public function getCycleTime()
    {
        return $this->cycleTime;
    }

    /**
     * Set deadline
     *
     * @param \DateTime $deadline
     * @return Task
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * Get deadline
     *
     * @return \DateTime 
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * Set acorn
     *
     * @param float $acorn
     * @return Task
     */
    public function setAcorn($acorn)
    {
        $this->acorn = $acorn;

        return $this;
    }

    /**
     * Get acorn
     *
     * @return float 
     */
    public function getAcorn()
    {
        return $this->acorn;
    }

    /**
     * Set medal
     *
     * @param integer $medal
     * @return Task
     */
    public function setMedal($medal)
    {
        $this->medal = $medal;

        return $this;
    }

    /**
     * Get medal
     *
     * @return integer 
     */
    public function getMedal()
    {
        return $this->medal;
    }

    /**
     * Set learns
     *
     * @param string $learns
     * @return Task
     */
    public function setLearns($learns)
    {
        $this->learns = $learns;

        return $this;
    }

    /**
     * Get learns
     *
     * @return string 
     */
    public function getLearns()
    {
        return $this->learns;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Task
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return Task
     */
    public function setAddTime($addTime)
    {
        $this->addTime = $addTime;

        return $this;
    }

    /**
     * Get addTime
     *
     * @return \DateTime 
     */
    public function getAddTime()
    {
        return $this->addTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     * @return Task
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime 
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Task
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     * @var integer
     */
    private $sid;


    /**
     * Set sid
     *
     * @param integer $sid
     * @return Task
     */
    public function setSid($sid)
    {
        $this->sid = $sid;

        return $this;
    }

    /**
     * Get sid
     *
     * @return integer 
     */
    public function getSid()
    {
        return $this->sid;
    }
    /**
     * @var integer
     */
    private $cycleUse;


    /**
     * Set cycleUse
     *
     * @param integer $cycleUse
     * @return Task
     */
    public function setCycleUse($cycleUse)
    {
        $this->cycleUse = $cycleUse;

        return $this;
    }

    /**
     * Get cycleUse
     *
     * @return integer 
     */
    public function getCycleUse()
    {
        return $this->cycleUse;
    }
    /**
     * @var string
     */
    private $executors;


    /**
     * Set executors
     *
     * @param string $executors
     * @return Task
     */
    public function setExecutors($executors)
    {
        $this->executors = $executors;

        return $this;
    }

    /**
     * Get executors
     *
     * @return string 
     */
    public function getExecutors()
    {
        return $this->executors;
    }
    /**
     * @var string
     */
    private $cycleStart;

    /**
     * @var string
     */
    private $cycleEnd;


    /**
     * Set cycleStart
     *
     * @param string $cycleStart
     * @return Task
     */
    public function setCycleStart($cycleStart)
    {
        $this->cycleStart = $cycleStart;

        return $this;
    }

    /**
     * Get cycleStart
     *
     * @return string 
     */
    public function getCycleStart()
    {
        return $this->cycleStart;
    }

    /**
     * Set cycleEnd
     *
     * @param string $cycleEnd
     * @return Task
     */
    public function setCycleEnd($cycleEnd)
    {
        $this->cycleEnd = $cycleEnd;

        return $this;
    }

    /**
     * Get cycleEnd
     *
     * @return string 
     */
    public function getCycleEnd()
    {
        return $this->cycleEnd;
    }
    /**
     * @var integer
     */
    private $acceptId;


    /**
     * Set acceptId
     *
     * @param integer $acceptId
     * @return Task
     */
    public function setAcceptId($acceptId)
    {
        $this->acceptId = $acceptId;

        return $this;
    }

    /**
     * Get acceptId
     *
     * @return integer 
     */
    public function getAcceptId()
    {
        return $this->acceptId;
    }
    /**
     * @var string
     */
    private $thumbs;


    /**
     * Set thumbs
     *
     * @param string $thumbs
     * @return Task
     */
    public function setThumbs($thumbs)
    {
        $this->thumbs = $thumbs;

        return $this;
    }

    /**
     * Get thumbs
     *
     * @return string 
     */
    public function getThumbs()
    {
        return $this->thumbs;
    }
    /**
     * @var integer
     */
    private $standardId;


    /**
     * Set standardId
     *
     * @param integer $standardId
     * @return Task
     */
    public function setStandardId($standardId)
    {
        $this->standardId = $standardId;

        return $this;
    }

    /**
     * Get standardId
     *
     * @return integer 
     */
    public function getStandardId()
    {
        return $this->standardId;
    }
    /**
     * @var string
     */
    private $excluders;


    /**
     * Set excluders
     *
     * @param string $excluders
     * @return Task
     */
    public function setExcluders($excluders)
    {
        $this->excluders = $excluders;

        return $this;
    }

    /**
     * Get excluders
     *
     * @return string 
     */
    public function getExcluders()
    {
        return $this->excluders;
    }
    /**
     * @var integer
     */
    private $astatus;


    /**
     * Set astatus
     *
     * @param integer $astatus
     * @return Task
     */
    public function setAstatus($astatus)
    {
        $this->astatus = $astatus;

        return $this;
    }

    /**
     * Get astatus
     *
     * @return integer 
     */
    public function getAstatus()
    {
        return $this->astatus;
    }
    /**
     * @var integer
     */
    private $codeNo;


    /**
     * Set codeNo
     *
     * @param integer $codeNo
     * @return Task
     */
    public function setCodeNo($codeNo)
    {
        $this->codeNo = $codeNo;

        return $this;
    }

    /**
     * Get codeNo
     *
     * @return integer 
     */
    public function getCodeNo()
    {
        return $this->codeNo;
    }
    /**
     * @var string
     */
    private $workload;


    /**
     * Set workload
     *
     * @param string $workload
     * @return Task
     */
    public function setWorkload($workload)
    {
        $this->workload = $workload;

        return $this;
    }

    /**
     * Get workload
     *
     * @return string 
     */
    public function getWorkload()
    {
        return $this->workload;
    }
    /**
     * @var integer
     */
    private $cycleTimes;

    /**
     * @var integer
     */
    private $cycleNext;


    /**
     * Set cycleTimes
     *
     * @param integer $cycleTimes
     * @return Task
     */
    public function setCycleTimes($cycleTimes)
    {
        $this->cycleTimes = $cycleTimes;

        return $this;
    }

    /**
     * Get cycleTimes
     *
     * @return integer 
     */
    public function getCycleTimes()
    {
        return $this->cycleTimes;
    }

    /**
     * Set cycleNext
     *
     * @param integer $cycleNext
     * @return Task
     */
    public function setCycleNext($cycleNext)
    {
        $this->cycleNext = $cycleNext;

        return $this;
    }

    /**
     * Get cycleNext
     *
     * @return integer 
     */
    public function getCycleNext()
    {
        return $this->cycleNext;
    }
    /**
     * @var integer
     */
    private $visibility;


    /**
     * Set visibility
     *
     * @param integer $visibility
     * @return Task
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * Get visibility
     *
     * @return integer 
     */
    public function getVisibility()
    {
        return $this->visibility;
    }
    /**
     * @var integer
     */
    private $standardTypes;


    /**
     * Set standardTypes
     *
     * @param integer $standardTypes
     * @return Task
     */
    public function setStandardTypes($standardTypes)
    {
        $this->standardTypes = $standardTypes;

        return $this;
    }

    /**
     * Get standardTypes
     *
     * @return integer 
     */
    public function getStandardTypes()
    {
        return $this->standardTypes;
    }
    /**
     * @var string
     */
    private $tags;

    /**
     * @var string
     */
    private $resolves;


    /**
     * Set tags
     *
     * @param string $tags
     * @return Task
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return string 
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set resolves
     *
     * @param string $resolves
     * @return Task
     */
    public function setResolves($resolves)
    {
        $this->resolves = $resolves;

        return $this;
    }

    /**
     * Get resolves
     *
     * @return string 
     */
    public function getResolves()
    {
        return $this->resolves;
    }
    /**
     * @var string
     */
    private $rechecks;


    /**
     * Set rechecks
     *
     * @param string $rechecks
     * @return Task
     */
    public function setRechecks($rechecks)
    {
        $this->rechecks = $rechecks;

        return $this;
    }

    /**
     * Get rechecks
     *
     * @return string 
     */
    public function getRechecks()
    {
        return $this->rechecks;
    }
    /**
     * @var \DateTime
     */
    private $acceptTime;


    /**
     * Set acceptTime
     *
     * @param \DateTime $acceptTime
     * @return Task
     */
    public function setAcceptTime($acceptTime)
    {
        $this->acceptTime = $acceptTime;

        return $this;
    }

    /**
     * Get acceptTime
     *
     * @return \DateTime 
     */
    public function getAcceptTime()
    {
        return $this->acceptTime;
    }
}
