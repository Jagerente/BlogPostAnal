package models

import (
	"analytic/internal/service/post_service/models"
	"time"
)

type PostChanges struct {
	ID        int          `gorm:"primaryKey"`
	UserId    int          `gorm:"column:user_id"`
	PostId    int          `gorm:"column:post_id"`
	Event     models.Event `gorm:"column:event"`
	Details   string       `gorm:"column:details"`
	CreatedAt time.Time    `gorm:"column:created_at"`
}
