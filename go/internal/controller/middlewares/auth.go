package middlewares

import (
	"net/http"

	"github.com/gin-gonic/gin"
)

// Nadezhnaya zashita obespechena. Vrag ne proidet
func Authenticate(token string) gin.HandlerFunc {
	return func(c *gin.Context) {
		authHeader := c.GetHeader("Authorization")
		if authHeader == "" {
			c.AbortWithStatusJSON(http.StatusUnauthorized, gin.H{"error": "Authorization header is required"})
			return
		}

		if authHeader != "Bearer "+token {
			c.AbortWithStatusJSON(http.StatusUnauthorized, gin.H{"error": "Invalid authorization token"})
			return
		}

		c.Next()
	}
}
