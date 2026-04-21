<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Application\Commands\WhatsNew;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use Exception;
use Slim\Exception\ContainerValueNotFoundException;

/**
 * Class GetWhatsNewCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\WhatsNew
 */
class GetWhatsNewCommandHandler extends CommandHandler
{
    /**
     * @param GetWhatsNewCommand $command
     *
     * @return CommandResult
     * @throws ContainerValueNotFoundException
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     * @throws Exception
     */
    public function handle(GetWhatsNewCommand $command)
    {
        if (!$this->getContainer()->getPermissionsService()->currentUserCanRead(Entities::DASHBOARD)) {
            throw new AccessDeniedException('You are not allowed to read news.');
        }

        $result = new CommandResult();
        $params = $command->getFields();

        try {
            $response = $this->getPosts($params);

            $result->setResult(CommandResult::RESULT_SUCCESS);
            $result->setMessage('Successfully retrieved posts.');
            $result->setData([
                'posts'               => $response['data'],
                'filteredPostsNumber' => $response['pagination']['total'],
            ]);
        } catch (Exception $e) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage('Failed to retrieve news: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    private function getPosts(array $params): array
    {
        $fullUrl = AMELIA_MIDDLEWARE_URL . 'blog-content?' . http_build_query($params);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $fullUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_USERAGENT => 'Amelia Plugin/1.0',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);

        if ($response === false) {
            throw new Exception('Failed to fetch posts from API: ' . $curlError);
        }

        if ($httpCode !== 200) {
            throw new Exception('Failed to fetch posts from API. HTTP Code: ' . $httpCode);
        }

        $content = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response from API: ' . json_last_error_msg());
        }

        return $content;
    }
}
