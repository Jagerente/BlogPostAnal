package models

type User struct {
	ID       int      `gorm:"primaryKey"`
	Email    string   `gorm:"column:email"`
	Roles    []string `gorm:"column:roles"`
	Password string   `gorm:"column:password"`
	Username string   `gorm:"column:username"`
	Posts    []*Post  `gorm:"foreignKey:PostsId"`
	PostsId  int      `gorm:"column:post_id"`
}
