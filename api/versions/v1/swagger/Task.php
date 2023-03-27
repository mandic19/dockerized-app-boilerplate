<?php

class Task
{
}

/**
 * @SWG\Definition(
 *      definition="Task",
 *      required={"section_id", "name"},
 *      @SWG\Property(
 *          property="id",
 *          type="integer",
 *          example="1"
 *      ),
 *      @SWG\Property(
 *          property="section_id",
 *          type="integer",
 *          example="1"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          type="string",
 *          description="Task name",
 *          example="Task 1"
 *      ),
 *      @SWG\Property(
 *          property="order",
 *          type="integer",
 *          description="Task order",
 *          example="1"
 *      ),
 * )
 *
 */


/**
 * @SWG\Get(
 *     path="/tasks",
 *     tags={"Task"},
 *     summary="Retrieve All Task",
 *     @SWG\Response(
 *         response = 200,
 *         description = "Get task list",
 *         @SWG\Items(
 *          type="array",
 *          items=@SWG\Schema(ref = "#/definitions/Task")
 *         )
 *     ),
 * )
 */

/**
 * @SWG\Get(
 *     path="/tasks/{id}",
 *     tags={"Task"},
 *     summary="Retrieve Task",
 *     @SWG\Parameter(
 *      in = "path",
 *      name = "id",
 *      required = true,
 *      type = "string",
 *    ),
 *     @SWG\Response(
 *         response = 200,
 *         description = "Get single task",
 *         @SWG\Schema(ref = "#/definitions/Task")
 *     ),
 * )
 */

/**
 * @SWG\Post(
 *    path = "/tasks",
 *    tags = {"Task"},
 *    operationId = "createTask",
 *    summary = "Create Task",
 *    description = "Creates new task",
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
 *              description="Task name",
 *              example="Task 1"
 *          ),
 *     )
 *  ),
 *  @SWG\Response(
 *      response = 200,
 *      description = "Success",
 *      @SWG\Schema(ref = "#/definitions/Task")
 *  ),
 *)
 */

/**
 * @SWG\Put(
 *    path = "/tasks/{id}",
 *    tags = {"Task"},
 *    operationId = "updateTask",
 *    summary = "Update Task",
 *    description = "Update existing task",
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
 *              property="section_id",
 *              type="string",
 *              description="Section ID",
 *              example="1"
 *          ),
 *          @SWG\Property(
 *              property="name",
 *              type="string",
 *              description="Task name",
 *              example="Task 1"
 *          ),
 *     )
 *  ),
 *  @SWG\Response(
 *      response = 200,
 *      description = "Success",
 *      @SWG\Schema(ref = "#/definitions/Task")
 *  ),
 *)
 */

/**
 * @SWG\Put(
 *    path = "/tasks/{id}/reorder",
 *    tags = {"Task"},
 *    operationId = "reorderTask",
 *    summary = "Reorder Task",
 *    description = "Update task order",
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
 *      @SWG\Schema(ref = "#/definitions/Task")
 *    ),
 *)
 */

/**
 * @SWG\Delete(
 *    path = "/tasks/{id}",
 *    tags = {"Task"},
 *    operationId = "deleteTask",
 *    summary = "Delete Task",
 *    description = "Delete existing task",
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
