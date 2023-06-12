package models

import "time"

type Post struct {
	ID            int        `gorm:"primaryKey"`
	Title         string     `gorm:"column:title"`
	Body          string     `gorm:"column:body"`
	Status        PostStatus `gorm:"column:status"`
	ModeratorNote string     `gorm:"column:moderator_note"`
	CreatedAt     time.Time  `gorm:"column:created_at"`
	Author        User       `gorm:"foreignKey:AuthorId"`
	AuthorId      User       `gorm:"column:author_id"`
}

type PostStatus string

var (
	PostPending  PostStatus = "pending"
	PostDeclined PostStatus = "declined"
	PostApproved PostStatus = "approved"
)
