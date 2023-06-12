package post_controller

import (
	"analytic/internal/controller/post_controller/mappers"
	"analytic/internal/controller/post_controller/models"
	"analytic/internal/service/post_service"
	"net/http"
	"time"

	"github.com/gin-gonic/gin"
	"go.uber.org/zap"
)

type Controller struct {
	service post_service.IPostService
	logger  *zap.Logger
}

type IPostController interface {
	Send(c *gin.Context)
}

func CreatePostController(service post_service.IPostService, logger *zap.Logger) *Controller {
	var log = logger.With(
		zap.String("controller", "post_controller"),
	)

	return &Controller{service: service, logger: log}
}

func (con *Controller) Send(c *gin.Context) {

	var logger = con.logger.With(
		zap.String("function", "Send"),
	)

	// Validate request
	var requestData models.Request
	if err := c.BindJSON(&requestData); err != nil {
		logger.Debug("Failed to validate data",
			zap.String("error", err.Error()))
		c.JSON(http.StatusBadRequest, gin.H{"error": "invalid request data"})
		return
	}

	logger.Debug("Object binded",
		zap.Any("Request", requestData),
	)

	// Start timer
	startTime := time.Now()

	serviceData := mappers.MapToService(requestData)
	logger.Debug("Sending to service",
		zap.Any("serviceData", serviceData))

	// Do something with our data
	if err := con.service.RegisterEvent(serviceData); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "internal server error"})
		return
	}

	// Get execution time
	elapsedTime := time.Since(startTime).Milliseconds()
	logger.Debug("Finished",
		zap.Any("time", elapsedTime))

	c.JSON(http.StatusOK, models.Response{
		Code: http.StatusOK,
		Time: elapsedTime,
	})
}
