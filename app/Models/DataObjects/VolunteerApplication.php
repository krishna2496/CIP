<?php

namespace App\Models\DataObjects;

class VolunteerApplication implements \JsonSerializable
{
    /**
     * @var string
     */
    private $applicationDate;

    /**
     * @var string
     */
    private $applicantMotivation;

    /**
     * @var string
     */
    private $applicationStatus;

    /**
     * @var string
     */
    private $missionName;

    /**
     * @var int
     */
    private $missionId;

    /**
     * @var string
     */
    private $missionType;

    /**
     * @var int
     */
    private $missionThemeId;

    /**
     * @var string
     */
    private $countryName;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var string
     */
    private $city;

    /**
     * @var array
     */
    private $missionSkills;

    /**
     * @var array
     */
    private $applicantSkills;

    /**
     * @var int
     */
    private $applicantId;

    /**
     * @var string
     */
    private $applicantFirstName;

    /**
     * @var string
     */
    private $applicantLastName;

    /**
     * @var string
     */
    private $applicantEmail;

    /**
     * @var string
     */
    private $applicantAvatar;

    /**
     * VolunteerApplication constructor.
     * @param $applicantId
     * @param $applicationDate
     * @param $applicationStatus
     * @param $missionId
     */
    public function __construct($applicantId, $applicationDate, $applicationStatus, $missionId)
    {
        $this->applicantId = $applicantId;
        $this->applicationDate = $applicationDate;
        $this->applicationStatus = $applicationStatus;
        $this->missionId = $missionId;
    }

    /**
     * @return string
     */
    public function getApplicationDate()
    {
        return $this->applicationDate;
    }

    /**
     * @return string
     */
    public function getApplicantMotivation()
    {
        return $this->applicantMotivation;
    }

    /**
     * @return string
     */
    public function getApplicationStatus()
    {
        return $this->applicationStatus;
    }

    /**
     * @return string
     */
    public function getMissionName()
    {
        return $this->missionName;
    }

    /**
     * @return int
     */
    public function getMissionId()
    {
        return $this->missionId;
    }

    /**
     * @return string
     */
    public function getMissionType()
    {
        return $this->missionType;
    }

    /**
     * @return string
     */
    public function getCountryName()
    {
        return $this->countryName;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return array
     */
    public function getMissionSkills()
    {
        return $this->missionSkills;
    }

    /**
     * @return array
     */
    public function getApplicantSkills()
    {
        return $this->applicantSkills;
    }

    /**
     * @return int
     */
    public function getApplicantId()
    {
        return $this->applicantId;
    }

    /**
     * @return string
     */
    public function getApplicantFirstName()
    {
        return $this->applicantFirstName;
    }

    /**
     * @return string
     */
    public function getApplicantLastName()
    {
        return $this->applicantLastName;
    }

    /**
     * @return string
     */
    public function getApplicantEmail()
    {
        return $this->applicantEmail;
    }

    /**
     * @return string
     */
    public function getApplicantAvatar()
    {
        return $this->applicantAvatar;
    }

    /**
     * @return int
     */
    public function getMissionThemeId(): int
    {
        return $this->missionThemeId;
    }

    /**
     * @param int $missionThemeId
     * @return VolunteerApplication
     */
    public function setMissionThemeId(int $missionThemeId): VolunteerApplication
    {
        $this->missionThemeId = $missionThemeId;
        return $this;
    }

    /**
     * @param $applicantMotivation
     * @return VolunteerApplication
     */
    public function setApplicantMotivation($applicantMotivation): VolunteerApplication
    {
        $this->applicantMotivation = $applicantMotivation;
        return $this;
    }

    /**
     * @param $applicationStatus
     * @return VolunteerApplication
     */
    public function setApplicationStatus($applicationStatus): VolunteerApplication
    {
        $this->applicationStatus = $applicationStatus;
        return $this;
    }

    /**
     * @param $missionName
     * @return VolunteerApplication
     */
    public function setMissionName($missionName): VolunteerApplication
    {
        $this->missionName = $missionName;
        return $this;
    }

    /**
     * @param $missionType
     * @return VolunteerApplication
     */
    public function setMissionType($missionType): VolunteerApplication
    {
        $this->missionType = $missionType;
        return $this;
    }

    /**
     * @param $countryName
     * @return VolunteerApplication
     */
    public function setCountryName($countryName): VolunteerApplication
    {
        $this->countryName = $countryName;
        return $this;
    }

    /**
     * @param $countryCode
     * @return VolunteerApplication
     */
    public function setCountryCode($countryCode): VolunteerApplication
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    /**
     * @param $city
     * @return VolunteerApplication
     */
    public function setCity($city): VolunteerApplication
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @param $missionSkills
     * @return VolunteerApplication
     */
    public function setMissionSkills($missionSkills): VolunteerApplication
    {
        $this->missionSkills = $missionSkills;
        return $this;
    }

    /**
     * @param $applicantSkills
     * @return VolunteerApplication
     */
    public function setApplicantSkills($applicantSkills): VolunteerApplication
    {
        $this->applicantSkills = $applicantSkills;
        return $this;
    }

    /**
     * @param $applicantFirstName
     * @return VolunteerApplication
     */
    public function setApplicantFirstName($applicantFirstName): VolunteerApplication
    {
        $this->applicantFirstName = $applicantFirstName;
        return $this;
    }

    /**
     * @param $applicantLastName
     * @return VolunteerApplication
     */
    public function setApplicantLastName($applicantLastName): VolunteerApplication
    {
        $this->applicantLastName = $applicantLastName;
        return $this;
    }

    /**
     * @param $applicantEmail
     * @return $this
     */
    public function setApplicantEmail($applicantEmail): VolunteerApplication
    {
        $this->applicantEmail = $applicantEmail;
        return $this;
    }

    /**
     * @param $applicantAvatar
     * @return $this
     */
    public function setApplicantAvatar($applicantAvatar): VolunteerApplication
    {
        $this->applicantAvatar = $applicantAvatar;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'applicationDate' => $this->applicationDate,
            'applicantMotivation' => $this->applicantMotivation,
            'applicationStatus' => $this->applicationStatus,
            'missionName' => $this->missionName,
            'missionId' => $this->missionId,
            'missionThemeId' => $this->missionThemeId,
            'missionType' => $this->missionType,
            'countryName' => $this->countryName,
            'countryCode' => $this->countryCode,
            'city' => $this->city,
            'missionSkills' => $this->missionSkills,
            'applicantId' => $this->applicantId,
            'applicantSkills' => $this->applicantSkills,
            'applicantFirstName' => $this->applicantFirstName,
            'applicantLastName' => $this->applicantLastName,
            'applicantEmail' => $this->applicantEmail,
            'applicantAvatar' => $this->applicantAvatar,
        ];
    }

}
