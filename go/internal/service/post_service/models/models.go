package models

import "time"

type PostData struct {
	UserId    int       `json:"user_id"`
	PostId    int       `json:"post_id"`
	Event     Event     `json:"event"`
	Details   string    `json:"details"`
	CreatedAt time.Time `json:"created_at"`
}

type Event string

var (
	EventPostCreated   = "EVENT_POST_CREATED"
	EventPostEdited    = "EVENT_POST_EDITED"
	EventPostDeleted   = "EVENT_POST_DELETED"
	EventPostModerated = "EVENT_POST_MODERATED"
)
