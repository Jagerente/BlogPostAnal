package middlewares

import (
	"github.com/gin-gonic/gin"
	"go.uber.org/zap"
)

// Authenticate extracts a user from the Authorization header
// It sets the user to the context if the user exists
func LogRawData(logger *zap.Logger) gin.HandlerFunc {
	return func(c *gin.Context) {
		// var log = logger.With(
		// 	zap.String("middleware", "LogRawData"),
		// )

		// rawRequest, err := c.GetRawData()
		// if err != nil {
		// 	log.Error("Failed to get raw data",
		// 		zap.String("Raw request", string(rawRequest)))
		// 	c.Next()
		// }

		// log.Debug("Object received",
		// 	zap.String("Raw request", string(rawRequest)))

		c.Next()
	}
}
