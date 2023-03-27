<?php

namespace api\models\definitions;

class Board
{
}

/**
 * @SWG\Definition(
 *      definition="Board",
 *      required={"name"},
 *      @SWG\Property(
 *          property="id",
 *          type="integer",
 *          example="1"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          type="string",
 *          description="Board name",
 *          example="Board 1"
 *      ),
 *      @SWG\Property(
 *          property="slug",
 *          type="string",
 *          description="Board name slug",
 *          example="board-1"
 *      ),
 * )
 */

/**
 * @SWG\Get(
 *     path="/boards",
 *     tags={"Board"},
 *     summary="Retrieve All Boards",
 *     @SWG\Response(
 *         response = 200,
 *         description = "Get board list",
 *         @SWG\Items(
 *          type="array",
 *          items=@SWG\Schema(ref = "#/definitions/Board")
 *         )
 *     ),
 * )
 */

/**
 * @SWG\Get(
 *     path="/boards/{id}",
 *     tags={"Board"},
 *     summary="Retrieve Board",
 *     @SWG\Parameter(
 *      in = "path",
 *      name = "id",
 *      required = true,
 *      type = "string",
 *    ),
 *     @SWG\Response(
 *         response = 200,
 *         description = "Get single board",
 *         @SWG\Schema(ref = "#/definitions/Board")
 *     ),
 * )
 */

/**
 * @SWG\Post(
 *    path = "/boards",
 *    tags = {"Board"},
 *    operationId = "createBoard",
 *    summary = "Create Board",
 *    description = "Creates new board",
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
 *              property="name",
 *              type="string",
 *              description="Board name",
 *              example="Board 1"
 *          ),
 *     )
 *  ),
 *  @SWG\Response(
 *      response = 200,
 *      description = "Success",
 *      @SWG\Schema(ref = "#/definitions/Board")
 *  ),
 *)
 */

/**
 * @SWG\Put(
 *    path = "/boards/{id}",
 *    tags = {"Board"},
 *    operationId = "updateBoard",
 *    summary = "Update Board",
 *    description = "Update existing board",
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
 *	  @SWG\Parameter(
 *      in = "body",
 *      name = "body",
 *      description = "Request body",
 *      type = "string",
 *      @SWG\Schema(
 *          @SWG\Property(
 *              property="name",
 *              type="string",
 *              description="Board name",
 *              example="Board 1"
 *          ),
 *     )
 *    ),
 *  @SWG\Response(
 *      response = 200,
 *      description = "Success",
 *      @SWG\Schema(ref = "#/definitions/Board")
 *  ),
 *)
 */

/**
 * @SWG\Delete(
 *    path = "/boards/{id}",
 *    tags = {"Board"},
 *    operationId = "deleteBoard",
 *    summary = "Delete Board",
 *    description = "Delete existing board",
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
