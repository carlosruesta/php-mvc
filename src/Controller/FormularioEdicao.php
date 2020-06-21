<?php

namespace Alura\Cursos\Controller;

use Alura\Cursos\Entity\Curso;
use Alura\Cursos\Helper\FlashMessageTrait;
use Alura\Cursos\Helper\RenderizadorDeHtmlTrait;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class FormularioEdicao implements RequestHandlerInterface
{
    use RenderizadorDeHtmlTrait, FlashMessageTrait;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ObjectRepository repositorioCursos */
    private $repositorioCursos;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repositorioCursos = $this->entityManager->getRepository(Curso::class);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parametros = $request->getQueryParams();
        $id = filter_var($parametros['id'], FILTER_VALIDATE_INT);

        if (is_null($id) || $id === false) {
            return new Response(302, ['Location' => '/listar-cursos']);
        }

        /** @var Curso $curso */
        $curso = $this->repositorioCursos->find($id);

        if (is_null($curso)) {
            $this->defineMensagem("danger", "Curso não identificado");
            return new Response(302, ['Location' => '/listar-cursos']);
        }

        $html = $this->renderizaHtml('cursos/formulario.php', [
            'curso' => $curso,
            'titulo' => 'Alterar curso ' . $curso->getDescricao()
        ]);

        return new Response(200, [], $html);
    }
}