<?php

namespace Overblog\GraphBundle\DependencyInjection\Compiler;

class FieldPass extends TaggedServiceMappingPass
{
    protected function getTagName()
    {
        return 'overblog_graph.field';
    }

    protected function getParameterName()
    {
        return 'overblog_graph.fields_mapping';
    }
}