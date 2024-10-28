<?php

namespace App\Entity;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class EventType extends AbstractEnumType
{
    public const string COMMIT = 'COM';
    public const string COMMENT = 'MSG';
    public const string PULL_REQUEST = 'PR';
    public const string COMMIT_COMMENT_EVENT = 'CommitCommentEvent';
    public const string CREATE_EVENT = 'CreateEvent';
    public const string DELETE_EVENT = 'DeleteEvent';
    public const string FORK_EVENT = 'ForkEvent';
    public const string GOLLUM_EVENT = 'GollumEvent';
    public const string ISSUE_COMMENT_EVENT = 'IssueCommentEvent';
    public const string ISSUES_EVENT = 'IssuesEvent';
    public const string MEMBER_EVENT = 'MemberEvent';
    public const string PUBLIC_EVENT = 'PublicEvent';
    public const string PULL_REQUEST_EVENT = 'PullRequestEvent';
    public const string PULL_REQUEST_REVIEW_EVENT = 'PullRequestReviewEvent';
    public const string PULL_REQUEST_REVIEW_COMMENT_EVENT = 'PullRequestReviewCommentEvent';
    public const string PUSH_EVENT = 'PushEvent';
    public const string RELEASE_EVENT = 'ReleaseEvent';
    public const string SPONSORSHIP_EVENT = 'SponsorshipEvent';
    public const string WATCH_EVENT = 'WatchEvent';
    public const string TEAM_ADD_EVENT = 'TeamAddEvent';
    public const string REPOSITORY_EVENT = 'RepositoryEvent';
    public const string DEPLOYMENT_EVENT = 'DeploymentEvent';
    public const string DEPLOYMENT_STATUS_EVENT = 'DeploymentStatusEvent';
    public const string PING_EVENT = 'PingEvent';
    public const string MARKETPLACE_PURCHASE_EVENT = 'MarketplacePurchaseEvent';
    public const string META_EVENT = 'MetaEvent';
    public const string ORGANIZATION_EVENT = 'OrganizationEvent';
    public const string PAGE_BUILD_EVENT = 'PageBuildEvent';
    public const string PROJECT_CARD_EVENT = 'ProjectCardEvent';
    public const string PROJECT_COLUMN_EVENT = 'ProjectColumnEvent';
    public const string PROJECT_EVENT = 'ProjectEvent';
    public const string CHECK_RUN_EVENT = 'CheckRunEvent';
    public const string CHECK_SUITE_EVENT = 'CheckSuiteEvent';
    public const string STATUS_EVENT = 'StatusEvent';
    public const string LABEL_EVENT = 'LabelEvent';
    public const string MILESTONE_EVENT = 'MilestoneEvent';
    public const string PACKAGE_EVENT = 'PackageEvent';
    public const string REPOSITORY_DISPATCH_EVENT = 'RepositoryDispatchEvent';

    protected static array $choices = [
        self::COMMIT_COMMENT_EVENT                => 'Commit Comment',
        self::CREATE_EVENT                        => 'Create',
        self::DELETE_EVENT                        => 'Delete',
        self::FORK_EVENT                          => 'Fork',
        self::GOLLUM_EVENT                        => 'Gollum',
        self::ISSUE_COMMENT_EVENT                 => 'Issue Comment',
        self::ISSUES_EVENT                        => 'Issues',
        self::MEMBER_EVENT                        => 'Member',
        self::PUBLIC_EVENT                        => 'Public',
        self::PULL_REQUEST_EVENT                  => 'Pull Request',
        self::PULL_REQUEST_REVIEW_EVENT           => 'Pull Request Review',
        self::PULL_REQUEST_REVIEW_COMMENT_EVENT   => 'Pull Request Review Comment',
        self::PUSH_EVENT                          => 'Push',
        self::RELEASE_EVENT                       => 'Release',
        self::SPONSORSHIP_EVENT                   => 'Sponsorship',
        self::WATCH_EVENT                         => 'Watch',
        self::TEAM_ADD_EVENT                      => 'Team Add',
        self::REPOSITORY_EVENT                    => 'Repository',
        self::DEPLOYMENT_EVENT                    => 'Deployment',
        self::DEPLOYMENT_STATUS_EVENT             => 'Deployment Status',
        self::PING_EVENT                          => 'Ping',
        self::MARKETPLACE_PURCHASE_EVENT          => 'Marketplace Purchase',
        self::META_EVENT                          => 'Meta',
        self::ORGANIZATION_EVENT                  => 'Organization',
        self::PAGE_BUILD_EVENT                    => 'Page Build',
        self::PROJECT_CARD_EVENT                  => 'Project Card',
        self::PROJECT_COLUMN_EVENT                => 'Project Column',
        self::PROJECT_EVENT                       => 'Project',
        self::CHECK_RUN_EVENT                     => 'Check Run',
        self::CHECK_SUITE_EVENT                   => 'Check Suite',
        self::STATUS_EVENT                        => 'Status',
        self::LABEL_EVENT                         => 'Label',
        self::MILESTONE_EVENT                     => 'Milestone',
        self::PACKAGE_EVENT                       => 'Package',
        self::REPOSITORY_DISPATCH_EVENT           => 'Repository Dispatch',
    ];
}
