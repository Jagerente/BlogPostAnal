package repositories

import (
	"analytic/internal/db/models"

	"gorm.io/gorm"
)

type Provider struct {
	gormConnection *gorm.DB
}

type IRepositoryProvider interface {
	CreatePostChangesRepo() IRepository[models.PostChanges, int]
}

func CreateProvider(connection *gorm.DB) *Provider {
	return &Provider{
		gormConnection: connection,
	}
}

func (provider *Provider) CreatePostChangesRepo() IRepository[models.PostChanges, int] {
	repo := &Repository[models.PostChanges, int]{
		BaseRepository{
			gormConnection: provider.gormConnection,
		},
	}

	return repo
}

type IRepository[T any, K comparable] interface {
	FindById(id K) (*T, error)
	FindBy(selector string, values ...string) (*[]T, error)
	FindAll() (*[]T, error)
	Add(value *T) (*T, error)
	Update(value *T) (*T, error)
	Remove(id K) error
}
