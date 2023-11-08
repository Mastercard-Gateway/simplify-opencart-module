all:
	@echo "Creating package: SimplifyCommerce.ocmod.zip"
	@git archive HEAD:src --format=zip -o SimplifyCommerce.ocmod.zip
