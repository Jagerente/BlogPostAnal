package mappers

import (
	db_models "analytic/internal/db/models"
	"analytic/internal/service/post_service/models"
)

func MapToDb(model models.PostData) db_models.PostChanges {
	return db_models.PostChanges{
		UserId:    model.UserId,
		PostId:    model.PostId,
		Details:   model.Details,
		Event:     model.Event,
		CreatedAt: model.CreatedAt,
	}
}
