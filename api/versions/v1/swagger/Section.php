<?php

class Section
{
}

/**
 * @SWG\Definition(
 *      definition="Section",
 *      required={"board_id", "name"},
 *      @SWG\Property(
 *          property="id",
 *          type="integer",
 *          example="1"
 *      ),
 *      @SWG\Property(
 *          property="board_id",
 *          type="integer",
 *          example="1"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          type="string",
 *          description="Section name",
 *          example="Section 1"
 *      ),
 *      @SWG\Property(
 *          property="order",
 *          type="integer",
 *          description="Section order",
 *          example="1"
 *      ),
 * )
 *
 */


/**
 * @SWG\Get(
 *     path="/sections",
 *     tags={"Section"},
 *     summary="Retrieve All Sections",
 *     @SWG\Response(
 *         response = 200,
 *         description = "Get sectrion list",
 *         @SWG\Items(
 *          type="array",
 *          items=@SWG\Schema(ref = "#/definitions/Section")
 *         )
 *     ),
 * )
 */

/**
 * @SWG\Get(
 *     path="/sections/{id}",
 *     tags={"Section"},
 *     summary="Retrieve Section",
 *     @SWG\Parameter(
 *      in = "path",
 *      name = "id",
 *      required = true,
 *      type = "string",
 *    ),
 *     @SWG\Response(
 *         response = 200,
 *         description = "Get single section",
 *         @SWG\Schema(ref = "#/definitions/Section")
 *     ),
 * )
 */

/**
 * @SWG\Post(
 *    path = "/sections",
 *    tags = {"Section"},
 *    operationId = "createSection",
 *    summary = "Create Section",
 *    description = "Creates new section",
 *    produces = {"application/json"},
 *    consumes = {"application/json"},
 *	@SWG\Parameter(
 *      in = "body",
 *      name = "body",
 *      description = "Request body",
 *      required = true,
 *      type = "string",
 *      @SWG\Schema(
 *          @SWG\Property(
 *              property="board_id",
 *              type="string",
 *              description="Board ID",
 *              example="1"
 *          ),
 *          @SWG\Property(
 *              property="name",
 *              type="string",
 *              description="Section name",
 *              example="Section 1"
 *          ),
 *     )
 *  ),
 *  @SWG\Response(
 *      response = 200,
 *      description = "Success",
 *      @SWG\Schema(ref = "#/definitions/Section")
 *  ),
 *)
 */

/**
 * @SWG\Put(
 *    path = "/sections/{id}",
 *    tags = {"Section"},
 *    operationId = "updateSection",
 *    summary = "Update Section",
 *    description = "Update existing section",
 *    produces = {"application/json"},
 *    consumes = {"application/json"},
 *
 *    @SWG\Parameter(
 *      in = "path",
 *      name = "id",
 *      required = true,
 *      type = "string",
 *    ),
 *
 *	@SWG\Parameter(
 *      in = "body",
 *      name = "body",
 *      description = "Request body",
 *      type = "string",
 *      @SWG\Schema(
 *          @SWG\Property(
 *              property="board_id",
 *              type="string",
 *              description="Board ID",
 *              example="1"
 *          ),
 *          @SWG\Property(
 *              property="name",
 *              type="string",
 *              description="Section name",
 *              example="Section 1"
 *          ),
 *     )
 *  ),
 *  @SWG\Response(
 *      response = 200,
 *      description = "Success",
 *      @SWG\Schema(ref = "#/definitions/Section")
 *  ),
 *)
 */


/**
 * @SWG\Put(
 *    path = "/sections/{id}/reorder",
 *    tags = {"Section"},
 *    operationId = "reorderSection",
 *    summary = "Reorder Section",
 *    description = "Update section order",
 *    produces = {"application/json"},
 *    consumes = {"application/json"},
 *
 *    @SWG\Parameter(
 *      in = "path",
 *      name = "id",
 *      required = true,
 *      type = "string",
 *    ),
 *
 *    @SWG\Parameter(
 *      in = "formData",
 *      name = "order",
 *      required = true,
 *      type = "integer",
 *    ),
 *
 *    @SWG\Response(
 *      response = 200,
 *      description = "Success",
 *      @SWG\Schema(ref = "#/definitions/Section")
 *    ),
 *)
 */

/**
 * @SWG\Delete(
 *    path = "/sections/{id}",
 *    tags = {"Section"},
 *    operationId = "deleteSection",
 *    summary = "Delete Section",
 *    description = "Delete existing section",
 *    produces = {"application/json"},
 *    consumes = {"application/json"},
 *
 *    @SWG\Parameter(
 *      in = "path",
 *      name = "id",
 *      required = true,
 *      type = "string",
 *    ),
 *
 *  @SWG\Response(
 *      response = 204,
 *      description = "Success",
 *  ),
 *)
 */
